<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TrialWebsiteAdminController extends Controller
{
    private array $reservedSlugs = [
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
    ];

    public function edit(int $id)
    {
        $trial = DB::table('trial_applications')->find($id);
        abort_unless($trial, 404);

        return view('admin.trials.edit', compact('trial'));
    }

    public function update(Request $request, int $id)
    {
        $trial = DB::table('trial_applications')->find($id);
        abort_unless($trial, 404);

        $data = $request->validate([
            'website_name' => ['required', 'string', 'max:150'],
            'business_name' => ['required', 'string', 'max:150'],
            'owner_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'desired_slug' => [
                'required',
                'alpha_dash',
                'max:100',
                Rule::notIn($this->reservedSlugs),
                Rule::unique(
                    'trial_applications',
                    'desired_slug'
                )->ignore($id),
            ],
            'category' => ['required', 'string', 'max:120'],
            'template_key' => [
                'required',
                Rule::in([
                    'modern',
                    'professional',
                    'creative',
                ]),
            ],
            'tagline' => ['nullable', 'string', 'max:250'],
            'about_business' => ['required', 'string', 'max:3000'],
            'services_offered' => ['nullable', 'string', 'max:3000'],
            'address' => ['nullable', 'string', 'max:1000'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'theme_color' => [
                'required',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'approved',
                    'rejected',
                    'expired',
                    'suspended',
                    'upgraded',
                ]),
            ],
            'expires_at' => ['nullable', 'date'],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $data['desired_slug'] = strtolower($data['desired_slug']);
        $data['trial_url'] =
            'https://'.$data['desired_slug'].'.mciedu.com';
        $data['updated_at'] = now();

        DB::table('trial_applications')
            ->where('id', $id)
            ->update($data);

        return redirect()
            ->route('admin.trial-websites.edit', $id)
            ->with(
                'success',
                'Trial website updated successfully.'
            );
    }

    public function suspend(int $id)
    {
        $trial = DB::table('trial_applications')->find($id);
        abort_unless($trial, 404);

        DB::table('trial_applications')
            ->where('id', $id)
            ->update([
                'status' => 'suspended',
                'updated_at' => now(),
            ]);

        return back()->with(
            'success',
            'Trial website suspended immediately.'
        );
    }

    public function restore(int $id)
    {
        $trial = DB::table('trial_applications')->find($id);
        abort_unless($trial, 404);

        $newExpiry = $trial->expires_at;

        if (
            !$newExpiry ||
            \Carbon\Carbon::parse($newExpiry)->isPast()
        ) {
            $newExpiry = now()->addDays(7);
        }

        DB::table('trial_applications')
            ->where('id', $id)
            ->update([
                'status' => 'approved',
                'expires_at' => $newExpiry,
                'updated_at' => now(),
            ]);

        return back()->with(
            'success',
            'Trial website restored successfully.'
        );
    }

    public function destroy(int $id)
    {
        $trial = DB::table('trial_applications')->find($id);
        abort_unless($trial, 404);

        DB::transaction(function () use ($id): void {
            DB::table('website_projects')
                ->where('trial_application_id', $id)
                ->update([
                    'trial_application_id' => null,
                    'updated_at' => now(),
                ]);

            DB::table('trial_applications')
                ->where('id', $id)
                ->delete();
        });

        return redirect()
            ->route('admin.trials.index')
            ->with(
                'success',
                'Trial website permanently deleted.'
            );
    }
}
