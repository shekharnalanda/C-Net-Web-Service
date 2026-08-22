<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TrialController extends Controller
{
    public function create()
    {
        return view('trial.apply');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:150'],
            'owner_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'desired_slug' => [
                'required',
                'alpha_dash',
                'max:100',
                Rule::unique('trial_applications', 'desired_slug'),
            ],
            'category' => ['required', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:250'],
            'about_business' => ['required', 'string', 'max:3000'],
            'services_offered' => ['nullable', 'string', 'max:3000'],
            'address' => ['nullable', 'string', 'max:1000'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'theme_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $data['desired_slug'] = Str::slug($data['desired_slug']);
        $data['status'] = 'pending';
        $data['created_at'] = now();
        $data['updated_at'] = now();

        DB::table('trial_applications')->insert($data);

        return back()->with(
            'success',
            'Application submitted successfully. Your trial website will be available after approval.'
        );
    }

    public function index()
    {
        $applications = DB::table('trial_applications')
            ->leftJoin('plans', 'trial_applications.selected_plan_id', '=', 'plans.id')
            ->select('trial_applications.*', 'plans.title as selected_plan_title')
            ->latest('trial_applications.created_at')
            ->get();

        $plans = DB::table('plans')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('admin.trials.index', compact('applications', 'plans'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected,expired'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $application = DB::table('trial_applications')->find($id);
        abort_unless($application, 404);

        $update = [
            'status' => $data['status'],
            'admin_notes' => $data['admin_notes'] ?? $application->admin_notes,
            'updated_at' => now(),
        ];

        if ($data['status'] === 'approved') {
            $update['starts_at'] = now();
            $update['expires_at'] = now()->addDays(10);
            $update['trial_url'] = 'https://' . $application->desired_slug . '.web.mciedu.com';
        }

        DB::table('trial_applications')->where('id', $id)->update($update);

        return back()->with('success', 'Trial status updated successfully.');
    }

    public function extend(int $id)
    {
        $application = DB::table('trial_applications')->find($id);
        abort_unless($application, 404);

        $currentExpiry = $application->expires_at
            ? \Carbon\Carbon::parse($application->expires_at)
            : now();

        if ($currentExpiry->isPast()) {
            $currentExpiry = now();
        }

        DB::table('trial_applications')->where('id', $id)->update([
            'status' => 'approved',
            'expires_at' => $currentExpiry->addDays(7),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Trial extended by 7 days.');
    }

    public function upgrade(Request $request, int $id)
    {
        $data = $request->validate([
            'selected_plan_id' => ['required', 'integer', 'exists:plans,id'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::table('trial_applications')->where('id', $id)->update([
            'status' => 'upgraded',
            'selected_plan_id' => $data['selected_plan_id'],
            'upgraded_at' => now(),
            'admin_notes' => $data['admin_notes'] ?? null,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Customer marked as upgraded to a paid plan.');
    }

    public function show(string $slug)
    {
        $business = DB::table('trial_applications')
            ->where('desired_slug', $slug)
            ->where('status', 'approved')
            ->where('expires_at', '>=', now())
            ->first();

        abort_unless($business, 404);

        return view('trial.website', compact('business'));
    }
}
