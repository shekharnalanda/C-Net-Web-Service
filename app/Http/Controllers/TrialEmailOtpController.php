<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class TrialEmailOtpController extends Controller
{
    private const OTP_EXPIRY_MINUTES = 5;
    private const MAX_ATTEMPTS = 3;
    private const RESEND_SECONDS = 60;

    public function send(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email:rfc', 'max:150'],
            'phone' => ['required', 'regex:/^[0-9+\-\s]{10,20}$/'],
        ], [
            'email.required' => 'Email address आवश्यक है।',
            'email.email' => 'कृपया सही email address दर्ज करें।',
            'phone.required' => 'Mobile number आवश्यक है।',
            'phone.regex' => 'कृपया सही mobile number दर्ज करें।',
        ]);

        $email = strtolower(trim((string) $request->input('email')));
        $rateKey = 'trial-email-otp:'.$request->ip().':'.$email;

        if (RateLimiter::tooManyAttempts($rateKey, 3)) {
            $seconds = RateLimiter::availableIn($rateKey);

            throw ValidationException::withMessages([
                'email' => "बहुत अधिक OTP requests हुई हैं। {$seconds} सेकंड बाद पुनः प्रयास करें।",
            ]);
        }

        RateLimiter::hit($rateKey, 600);

        $otp = (string) random_int(100000, 999999);

        $request->session()->put('trial_email_otp', [
            'email' => $email,
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES)->timestamp,
            'attempts' => 0,
            'last_sent_at' => now()->timestamp,
            'payload' => $request->except(['_token', 'otp']),
        ]);

        try {
            $this->sendOtpMail($email, $otp);
        } catch (Throwable $exception) {
            report($exception);
            $request->session()->forget('trial_email_otp');

            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'OTP email अभी भेजा नहीं जा सका। कृपया कुछ देर बाद पुनः प्रयास करें।',
                ]);
        }

        return redirect()
            ->route('trial.email.verify.form')
            ->with('success', 'Verification OTP आपके email पर भेजा गया है।');
    }

    public function form(Request $request)
    {
        $pending = $request->session()->get('trial_email_otp');

        if (!$pending || empty($pending['email'])) {
            return redirect()
                ->route('trial.apply')
                ->withErrors([
                    'email' => 'पहले Trial Website form भरकर OTP प्राप्त करें।',
                ]);
        }

        return view('trial.verify-email', [
            'email' => $this->maskEmail($pending['email']),
            'resendAfter' => max(
                0,
                self::RESEND_SECONDS - (now()->timestamp - ($pending['last_sent_at'] ?? 0))
            ),
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ], [
            'otp.required' => '6 अंकों का OTP दर्ज करें।',
            'otp.digits' => 'OTP ठीक 6 अंकों का होना चाहिए।',
        ]);

        $pending = $request->session()->get('trial_email_otp');

        if (!$pending || empty($pending['payload'])) {
            return redirect()
                ->route('trial.apply')
                ->withErrors([
                    'email' => 'Verification session समाप्त हो गया। कृपया form दोबारा भरें।',
                ]);
        }

        if (now()->timestamp > ($pending['expires_at'] ?? 0)) {
            $request->session()->forget('trial_email_otp');

            return redirect()
                ->route('trial.apply')
                ->withErrors([
                    'email' => 'OTP expire हो गया। कृपया form दोबारा भरकर नया OTP प्राप्त करें।',
                ]);
        }

        $attempts = (int) ($pending['attempts'] ?? 0);

        if ($attempts >= self::MAX_ATTEMPTS) {
            $request->session()->forget('trial_email_otp');

            return redirect()
                ->route('trial.apply')
                ->withErrors([
                    'email' => 'OTP attempts की सीमा पूरी हो गई। कृपया दोबारा शुरू करें।',
                ]);
        }

        if (!Hash::check((string) $request->input('otp'), $pending['otp_hash'])) {
            $pending['attempts'] = $attempts + 1;
            $request->session()->put('trial_email_otp', $pending);

            $remaining = self::MAX_ATTEMPTS - $pending['attempts'];

            if ($remaining <= 0) {
                $request->session()->forget('trial_email_otp');

                return redirect()
                    ->route('trial.apply')
                    ->withErrors([
                        'email' => 'तीन बार गलत OTP डाला गया। कृपया form दोबारा भरें।',
                    ]);
            }

            throw ValidationException::withMessages([
                'otp' => "OTP सही नहीं है। {$remaining} प्रयास शेष हैं।",
            ]);
        }

        $payload = $pending['payload'];
        $verifiedEmail = strtolower(trim((string) ($payload['email'] ?? '')));

        if (!hash_equals($pending['email'], $verifiedEmail)) {
            $request->session()->forget('trial_email_otp');

            return redirect()
                ->route('trial.apply')
                ->withErrors([
                    'email' => 'Email verification मेल नहीं खाता। कृपया दोबारा प्रयास करें।',
                ]);
        }

        $request->session()->forget('trial_email_otp');
        $request->session()->put(
            'trial_verified_email',
            [
                'email' => $verifiedEmail,
                'verified_at' => now()->timestamp,
            ]
        );

        $trialRequest = Request::create(
            '/trial/apply',
            'POST',
            $payload
        );

        $trialRequest->setLaravelSession($request->session());
        $trialRequest->setUserResolver($request->getUserResolver());

        return app(TrialController::class)->store($trialRequest);
    }

    public function resend(Request $request)
    {
        $pending = $request->session()->get('trial_email_otp');

        if (!$pending || empty($pending['email']) || empty($pending['payload'])) {
            return redirect()
                ->route('trial.apply')
                ->withErrors([
                    'email' => 'Verification session उपलब्ध नहीं है। कृपया दोबारा शुरू करें।',
                ]);
        }

        $elapsed = now()->timestamp - ($pending['last_sent_at'] ?? 0);

        if ($elapsed < self::RESEND_SECONDS) {
            $wait = self::RESEND_SECONDS - $elapsed;

            return back()->withErrors([
                'otp' => "नया OTP प्राप्त करने के लिए {$wait} सेकंड प्रतीक्षा करें।",
            ]);
        }

        $resendKey = 'trial-email-otp-resend:'.$request->ip().':'.$pending['email'];

        if (RateLimiter::tooManyAttempts($resendKey, 3)) {
            $seconds = RateLimiter::availableIn($resendKey);

            return back()->withErrors([
                'otp' => "Resend limit पूरी हो गई। {$seconds} सेकंड बाद प्रयास करें।",
            ]);
        }

        RateLimiter::hit($resendKey, 600);

        $otp = (string) random_int(100000, 999999);

        $pending['otp_hash'] = Hash::make($otp);
        $pending['expires_at'] = now()->addMinutes(self::OTP_EXPIRY_MINUTES)->timestamp;
        $pending['attempts'] = 0;
        $pending['last_sent_at'] = now()->timestamp;

        $request->session()->put('trial_email_otp', $pending);

        try {
            $this->sendOtpMail($pending['email'], $otp);
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'otp' => 'OTP दोबारा भेजा नहीं जा सका। कृपया कुछ देर बाद प्रयास करें।',
            ]);
        }

        return back()->with('success', 'नया OTP आपके email पर भेज दिया गया है।');
    }

    private function sendOtpMail(string $email, string $otp): void
    {
        $safeOtp = e($otp);

        $html = <<<HTML
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>C-Net Web Services Email Verification</title>
</head>
<body style="margin:0;background:#f1f5f9;font-family:Arial,sans-serif;color:#172033">
<div style="max-width:600px;margin:28px auto;background:#ffffff;border-radius:14px;overflow:hidden;border:1px solid #dce5ef">
<div style="background:#063b71;padding:25px;text-align:center;color:#ffffff">
<h1 style="margin:0;font-size:25px">C-Net Web Services</h1>
<p style="margin:7px 0 0">Email Verification</p>
</div>
<div style="padding:28px">
<p>नमस्कार,</p>
<p>आपकी Trial Website request verify करने के लिए OTP नीचे दिया गया है:</p>
<div style="font-size:34px;font-weight:bold;letter-spacing:9px;text-align:center;color:#063b71;background:#eef6ff;padding:18px;border-radius:10px;margin:22px 0">
{$safeOtp}
</div>
<p><strong>यह OTP 5 मिनट तक valid है।</strong></p>
<p>यदि आपने Trial Website के लिए request नहीं की है, तो इस email को ignore करें। OTP किसी के साथ साझा न करें।</p>
</div>
<div style="padding:18px;text-align:center;background:#f8fafc;color:#64748b;font-size:13px">
C-Net Web Services<br>
<a href="https://web.mciedu.com" style="color:#0756a3">web.mciedu.com</a>
</div>
</div>
</body>
</html>
HTML;

        Mail::html($html, function ($message) use ($email): void {
            $message
                ->to($email)
                ->from(
                    config('mail.from.address'),
                    'C-Net Web Services'
                )
                ->subject('C-Net Web Services – Email Verification OTP');
        });
    }

    private function maskEmail(string $email): string
    {
        [$name, $domain] = array_pad(explode('@', $email, 2), 2, '');

        $visible = mb_substr($name, 0, min(2, mb_strlen($name)));
        $masked = $visible.str_repeat('*', max(3, mb_strlen($name) - 2));

        return $masked.'@'.$domain;
    }
}
