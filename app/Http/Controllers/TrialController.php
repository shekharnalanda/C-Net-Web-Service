<?php

namespace App\Http\Controllers;

use App\Services\AdminNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TrialController extends Controller
{
    private const RESERVED_SLUGS = [
        'www',
        'web',
        'mail',
        'email',
        'admin',
        'administrator',
        'login',
        'logout',
        'api',
        'app',
        'apps',
        'support',
        'help',
        'billing',
        'payment',
        'payments',
        'account',
        'accounts',
        'dashboard',
        'trial',
        'trials',
        'test',
        'demo',
        'ftp',
        'cpanel',
        'whm',
        'server',
        'secure',
        'ssl',
        'static',
        'assets',
        'cdn',
    ];

    private const TEMPLATES = [
        'modern',
        'professional',
        'creative',
        'education-pro',
        'business-pro',
    ];

    public function create()
    {
        return view('trial.apply', [
            'templates' => self::TEMPLATES,
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'desired_slug' => Str::slug(
                strtolower((string) $request->input('desired_slug'))
            ),
        ]);

        $data = $request->validate([
            'website_name' => ['required', 'string', 'max:150'],
            'business_name' => ['required', 'string', 'max:150'],
            'owner_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'min:8', 'max:20'],
            'email' => ['required', 'email', 'max:150'],
            'desired_slug' => [
                'required',
                Rule::notIn([
                    'www',
                    'web',
                    'mail',
                    'cpanel',
                    'webmail',
                    'webdisk',
                    'ftp',
                    'autodiscover',
                    'cpcontacts',
                    'cpcalendars',
                    'cnet',
                    'library',
                    'cnetlibrary',
                    'cnetcomputer',
                    'pathshala',
                    'admin',
                    'api',
                    'support',
                ]),
                'regex:/^[a-z0-9][a-z0-9-]{2,62}$/',
                Rule::notIn(self::RESERVED_SLUGS),
                Rule::unique('trial_applications', 'desired_slug'),
            ],
            'category' => ['required', 'string', 'max:120'],
            'template_key' => [
                'required',
                Rule::in(self::TEMPLATES),
            ],
            'tagline' => ['nullable', 'string', 'max:250'],
            'about_business' => ['required', 'string', 'max:3000'],
            'services_offered' => ['required', 'string', 'max:3000'],
            'address' => ['required', 'string', 'max:1000'],
            'whatsapp' => ['required', 'string', 'min:8', 'max:20'],
            'theme_color' => [
                'required',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'terms_accepted' => ['required', 'accepted'],
        ], [
            'desired_slug.regex' =>
                'Website name must contain only lowercase letters, numbers and hyphens.',
            'desired_slug.not_in' =>
                'This website name is reserved. Please select another name.',
            'desired_slug.unique' =>
                'This website name is already in use.',
        ]);

        $now = now();
        $trialUrl = 'https://'.$data['desired_slug'].'.mciedu.com';

        $data['website_name'] =
            $data['website_name'] ?: $data['business_name'];

        $data['template_key'] =
            $data['template_key'] ?: 'modern';

        unset($data['terms_accepted']);

        $data['status'] = 'approved';
        $data['trial_url'] = $trialUrl;
        $data['starts_at'] = $now;
        $data['expires_at'] = $now->copy()->addDays(7);
        $data['consent_at'] = $request->boolean('terms_accepted')
            ? $now
            : null;
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        DB::table('trial_applications')->insert($data);

        AdminNotificationService::sendTrialApplication($data);

        return back()
            ->with(
                'success',
                'Your free website has been created successfully and is active for 7 days.'
            )
            ->with('trial_url', $trialUrl)
            ->with(
                'trial_expires_at',
                Carbon::parse($data['expires_at'])->format('d M Y, h:i A')
            );
    }

    public function index()
    {
        $applications = DB::table('trial_applications')
            ->leftJoin(
                'plans',
                'trial_applications.selected_plan_id',
                '=',
                'plans.id'
            )
            ->select(
                'trial_applications.*',
                'plans.title as selected_plan_title'
            )
            ->latest('trial_applications.created_at')
            ->get();

        $plans = DB::table('plans')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view(
            'admin.trials.index',
            compact('applications', 'plans')
        );
    }

    public function updateStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => [
                'required',
                'in:pending,approved,suspended,rejected,expired,upgraded',
            ],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $application = DB::table('trial_applications')->find($id);
        abort_unless($application, 404);

        $update = [
            'status' => $data['status'],
            'admin_notes' =>
                $data['admin_notes'] ?? $application->admin_notes,
            'updated_at' => now(),
        ];

        if ($data['status'] === 'approved') {
            $update['starts_at'] = $application->starts_at ?: now();
            $update['expires_at'] =
                $application->expires_at
                    && Carbon::parse($application->expires_at)->isFuture()
                ? $application->expires_at
                : now()->addDays(7);

            $update['trial_url'] =
                'https://'.$application->desired_slug.'.mciedu.com';

            $update['suspended_at'] = null;
            $update['expired_at'] = null;
        }

        if ($data['status'] === 'suspended') {
            $update['suspended_at'] = now();
        }

        if ($data['status'] === 'expired') {
            $update['expired_at'] = now();
        }

        DB::table('trial_applications')
            ->where('id', $id)
            ->update($update);

        return back()->with(
            'success',
            'Trial website status updated successfully.'
        );
    }

    public function suspend(int $id)
    {
        $application = DB::table('trial_applications')->find($id);
        abort_unless($application, 404);

        DB::table('trial_applications')
            ->where('id', $id)
            ->update([
                'status' => 'suspended',
                'suspended_at' => now(),
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Trial website suspended.');
    }

    public function restore(int $id)
    {
        $application = DB::table('trial_applications')->find($id);
        abort_unless($application, 404);

        DB::table('trial_applications')
            ->where('id', $id)
            ->update([
                'status' => 'approved',
                'suspended_at' => null,
                'expired_at' => null,
                'expires_at' => now()->addDays(7),
                'trial_url' =>
                    'https://'.$application->desired_slug.'.mciedu.com',
                'updated_at' => now(),
            ]);

        return back()->with(
            'success',
            'Trial website restored for 7 days.'
        );
    }

    public function extend(int $id)
    {
        $application = DB::table('trial_applications')->find($id);
        abort_unless($application, 404);

        $currentExpiry = $application->expires_at
            ? Carbon::parse($application->expires_at)
            : now();

        if ($currentExpiry->isPast()) {
            $currentExpiry = now();
        }

        DB::table('trial_applications')
            ->where('id', $id)
            ->update([
                'status' => 'approved',
                'expires_at' => $currentExpiry->addDays(7),
                'expired_at' => null,
                'suspended_at' => null,
                'trial_url' =>
                    'https://'.$application->desired_slug.'.mciedu.com',
                'updated_at' => now(),
            ]);

        return back()->with(
            'success',
            'Trial extended by 7 days.'
        );
    }

    public function upgrade(Request $request, int $id)
    {
        $data = $request->validate([
            'selected_plan_id' => [
                'required',
                'integer',
                'exists:plans,id',
            ],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::table('trial_applications')
            ->where('id', $id)
            ->update([
                'status' => 'upgraded',
                'selected_plan_id' => $data['selected_plan_id'],
                'upgraded_at' => now(),
                'admin_notes' => $data['admin_notes'] ?? null,
                'expired_at' => null,
                'suspended_at' => null,
                'updated_at' => now(),
            ]);

        return back()->with(
            'success',
            'Customer upgraded to a paid plan.'
        );
    }

    public function destroy(int $id)
    {
        $application = DB::table('trial_applications')->find($id);
        abort_unless($application, 404);

        abort_if(
            $application->status === 'upgraded',
            422,
            'Upgraded websites cannot be deleted as trial data.'
        );

        DB::table('trial_applications')
            ->where('id', $id)
            ->delete();

        return back()->with(
            'success',
            'Trial website and client trial record deleted permanently.'
        );
    }

    public function show(string $slug)
    {
        $business = DB::table('trial_applications')
            ->where('desired_slug', $slug)
            ->first();

        abort_unless($business, 404);

        if (
            $business->status === 'upgraded'
            || (
                $business->status === 'approved'
                && $business->expires_at
                && Carbon::parse($business->expires_at)->isFuture()
            )
        ) {
            return view('trial.website', compact('business'));
        }

        if (
            $business->status === 'approved'
            && $business->expires_at
            && Carbon::parse($business->expires_at)->isPast()
        ) {
            DB::table('trial_applications')
                ->where('id', $business->id)
                ->update([
                    'status' => 'expired',
                    'expired_at' => now(),
                    'updated_at' => now(),
                ]);

            $business->status = 'expired';
        }

        if (in_array($business->status, ['expired', 'suspended'], true)) {
            return response()
                ->view('trial.expired', compact('business'), 410);
        }

        abort(404);
    }
}
