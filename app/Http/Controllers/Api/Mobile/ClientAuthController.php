<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class ClientAuthController extends Controller
{
    public function requestOtp(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email:rfc', 'max:150']]);
        $email = Str::lower(trim($data['email']));

        if (!DB::table('trial_applications')->whereRaw('LOWER(email) = ?', [$email])->exists()) {
            throw ValidationException::withMessages([
                'email' => 'No Trial Website or client project was found for this email.',
            ]);
        }

        $key = 'mobile-client-otp:'.$request->ip().':'.$email;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            throw ValidationException::withMessages([
                'email' => 'Too many OTP requests. Please try again later.',
            ]);
        }
        RateLimiter::hit($key, 600);

        $otp = (string) random_int(100000, 999999);
        DB::table('mobile_login_otps')->where('email', $email)->whereNull('used_at')->delete();
        DB::table('mobile_login_otps')->insert([
            'email' => $email,
            'otp_hash' => Hash::make($otp),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(5),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            Mail::html($this->emailHtml($otp), function ($message) use ($email): void {
                $message->to($email)
                    ->from(config('mail.from.address'), 'C-Net Web Services')
                    ->subject('C-Net Web Services App Login OTP');
            });
        } catch (Throwable $exception) {
            report($exception);
            DB::table('mobile_login_otps')->where('email', $email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'OTP email could not be sent. Please try again.',
            ], 503);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your registered email.',
            'expires_in' => 300,
        ]);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:150'],
            'otp' => ['required', 'digits:6'],
            'device_name' => ['nullable', 'string', 'max:150'],
        ]);
        $email = Str::lower(trim($data['email']));

        $record = DB::table('mobile_login_otps')
            ->where('email', $email)->whereNull('used_at')->latest('id')->first();

        if (!$record || now()->greaterThan($record->expires_at)) {
            throw ValidationException::withMessages(['otp' => 'OTP has expired. Request a new OTP.']);
        }
        if ($record->attempts >= 3) {
            throw ValidationException::withMessages(['otp' => 'OTP attempt limit reached.']);
        }
        if (!Hash::check($data['otp'], $record->otp_hash)) {
            DB::table('mobile_login_otps')->where('id', $record->id)->increment('attempts');
            throw ValidationException::withMessages(['otp' => 'The OTP is incorrect.']);
        }

        DB::table('mobile_login_otps')->where('id', $record->id)->update([
            'used_at' => now(), 'updated_at' => now(),
        ]);

        $plainToken = Str::random(80);
        DB::table('mobile_client_tokens')->insert([
            'email' => $email,
            'token_hash' => hash('sha256', $plainToken),
            'device_name' => $data['device_name'] ?? 'C-Net Web Services App',
            'expires_at' => now()->addDays(30),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'token' => $plainToken,
            'token_type' => 'Bearer',
            'expires_in' => 2592000,
            'client' => $this->clientSummary($email),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        if ($token) {
            DB::table('mobile_client_tokens')->where('token_hash', hash('sha256', $token))->delete();
        }
        return response()->json(['success' => true, 'message' => 'Signed out successfully.']);
    }

    private function clientSummary(string $email): array
    {
        $trial = DB::table('trial_applications')->whereRaw('LOWER(email) = ?', [$email])->latest('id')->first();
        return [
            'email' => $email,
            'name' => $trial->owner_name ?? 'C-Net Client',
            'business_name' => $trial->business_name ?? null,
        ];
    }

    private function emailHtml(string $otp): string
    {
        $safeOtp = e($otp);
        return <<<HTML
<!doctype html><html><body style="margin:0;background:#f1f5f9;font-family:Arial,sans-serif">
<div style="max-width:580px;margin:28px auto;background:#fff;border-radius:14px;overflow:hidden">
<div style="background:#063b71;color:#fff;padding:24px;text-align:center"><h1>C-Net Web Services</h1><p>Mobile App Login</p></div>
<div style="padding:28px;color:#172033"><p>Your secure app login OTP is:</p>
<div style="font-size:34px;font-weight:bold;letter-spacing:9px;text-align:center;color:#063b71;background:#eef6ff;padding:18px;border-radius:10px">{$safeOtp}</div>
<p><strong>This OTP is valid for 5 minutes.</strong></p><p>Do not share this OTP with anyone.</p></div>
</div></body></html>
HTML;
    }
}
