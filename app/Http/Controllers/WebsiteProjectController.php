<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WebsiteProjectController extends Controller
{
    public function index()
    {
        $projects = DB::table('website_projects')
            ->leftJoin(
                'trial_applications',
                'website_projects.trial_application_id',
                '=',
                'trial_applications.id'
            )
            ->leftJoin('plans', 'website_projects.plan_id', '=', 'plans.id')
            ->select(
                'website_projects.*',
                'trial_applications.business_name',
                'trial_applications.owner_name',
                'trial_applications.phone',
                'trial_applications.email',
                'trial_applications.trial_url',
                'plans.title as plan_title'
            )
            ->latest('website_projects.created_at')
            ->get();

        $convertibleTrials = DB::table('trial_applications')
            ->leftJoin(
                'website_projects',
                'trial_applications.id',
                '=',
                'website_projects.trial_application_id'
            )
            ->whereNull('website_projects.id')
            ->whereIn('trial_applications.status', [
                'pending',
                'approved',
                'expired',
                'suspended',
            ])
            ->select('trial_applications.*')
            ->latest('trial_applications.created_at')
            ->get();

        return view(
            'admin.projects.index',
            compact('projects', 'convertibleTrials')
        );
    }

    public function create(int $trialId)
    {
        $trial = DB::table('trial_applications')->find($trialId);
        abort_unless($trial, 404);

        $existingProject = DB::table('website_projects')
            ->where('trial_application_id', $trialId)
            ->first();

        if ($existingProject) {
            return redirect()
                ->route('admin.projects.show', $existingProject->id);
        }

        $plans = DB::table('plans')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view(
            'admin.projects.convert',
            compact('trial', 'plans')
        );
    }

    public function store(Request $request, int $trialId)
    {
        $trial = DB::table('trial_applications')->find($trialId);
        abort_unless($trial, 404);

        $domain = strtolower(trim((string) $request->input('custom_domain')));
        $domain = preg_replace('#^https?://#i', '', $domain);
        $domain = preg_replace('#/.*$#', '', $domain);
        $domain = preg_replace('/^www\./i', '', $domain);

        $request->merge(['custom_domain' => $domain]);

        $data = $request->validate([
            'project_name' => ['required', 'string', 'max:180'],
            'project_type' => [
                'required',
                Rule::in([
                    'full_website',
                    'dynamic_website',
                    'online_store',
                ]),
            ],
            'selected_plan_id' => [
                'nullable',
                'integer',
                'exists:plans,id',
            ],
            'custom_domain' => [
                'required',
                'max:190',
                'regex:/^(?=.{4,190}$)(?!-)(?:[a-z0-9-]+\.)+[a-z]{2,63}$/',
                'unique:website_projects,custom_domain',
            ],
            'requirements' => ['nullable', 'string', 'max:10000'],
            'quoted_amount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_status' => [
                'required',
                Rule::in(['pending', 'partial', 'paid']),
            ],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $checklist = [
            ['key' => 'client_verified', 'label' => 'Client details verified', 'done' => true],
            ['key' => 'plan_confirmed', 'label' => 'Website type and plan confirmed', 'done' => true],
            ['key' => 'payment_confirmed', 'label' => 'Payment confirmed', 'done' => $data['payment_status'] === 'paid'],
            ['key' => 'domain_access', 'label' => 'Domain purchased or access received', 'done' => false],
            ['key' => 'dns_configured', 'label' => 'Domain DNS configured', 'done' => false],
            ['key' => 'workspace_created', 'label' => 'Production workspace created', 'done' => false],
            ['key' => 'content_transferred', 'label' => 'Trial content transferred', 'done' => false],
            ['key' => 'design_approved', 'label' => 'Design approved by client', 'done' => false],
            ['key' => 'modules_configured', 'label' => 'Required modules configured', 'done' => false],
            ['key' => 'ssl_enabled', 'label' => 'SSL certificate enabled', 'done' => false],
            ['key' => 'email_configured', 'label' => 'Business email configured', 'done' => false],
            ['key' => 'seo_analytics', 'label' => 'SEO and analytics configured', 'done' => false],
            ['key' => 'testing_passed', 'label' => 'Final testing passed', 'done' => false],
            ['key' => 'client_approval', 'label' => 'Final client approval received', 'done' => false],
            ['key' => 'website_launched', 'label' => 'Website launched on custom domain', 'done' => false],
        ];

        $projectId = DB::transaction(function () use (
            $trial,
            $data,
            $checklist
        ) {
            $now = now();

            $projectId = DB::table('website_projects')->insertGetId([
                'trial_application_id' => $trial->id,
                'plan_id' => $data['selected_plan_id'] ?? null,
                'project_name' => $data['project_name'],
                'project_type' => $data['project_type'],
                'custom_domain' => $data['custom_domain'],
                'requirements' => $data['requirements'] ?? null,
                'quoted_amount' => $data['quoted_amount'] ?? null,
                'paid_amount' => $data['paid_amount'] ?? 0,
                'payment_status' => $data['payment_status'],
                'project_status' => 'planning',
                'deployment_checklist' => json_encode(
                    $checklist,
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                ),
                'admin_notes' => $data['admin_notes'] ?? null,
                'converted_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('trial_applications')
                ->where('id', $trial->id)
                ->update([
                    'status' => 'upgraded',
                    'selected_plan_id' => $data['selected_plan_id'] ?? null,
                    'expires_at' => null,
                    'upgraded_at' => $now,
                    'admin_notes' => $data['admin_notes']
                        ?? $trial->admin_notes,
                    'updated_at' => $now,
                ]);

            return $projectId;
        });

        return redirect()
            ->route('admin.projects.show', $projectId)
            ->with(
                'success',
                'Trial successfully converted into a live website project.'
            );
    }

    public function show(int $id)
    {
        $project = DB::table('website_projects')
            ->leftJoin(
                'trial_applications',
                'website_projects.trial_application_id',
                '=',
                'trial_applications.id'
            )
            ->leftJoin('plans', 'website_projects.plan_id', '=', 'plans.id')
            ->select(
                'website_projects.*',
                'trial_applications.website_name',
                'trial_applications.business_name',
                'trial_applications.owner_name',
                'trial_applications.phone',
                'trial_applications.email',
                'trial_applications.whatsapp',
                'trial_applications.category',
                'trial_applications.tagline',
                'trial_applications.about_business',
                'trial_applications.services_offered',
                'trial_applications.address',
                'trial_applications.trial_url',
                'plans.title as plan_title'
            )
            ->where('website_projects.id', $id)
            ->first();

        abort_unless($project, 404);

        $checklist = json_decode(
            $project->deployment_checklist ?: '[]',
            true
        ) ?: [];

        return view(
            'admin.projects.show',
            compact('project', 'checklist')
        );
    }

    public function update(Request $request, int $id)
    {
        $project = DB::table('website_projects')->find($id);
        abort_unless($project, 404);

        $data = $request->validate([
            'project_status' => [
                'required',
                Rule::in([
                    'planning',
                    'domain_setup',
                    'development',
                    'testing',
                    'ready_to_launch',
                    'launched',
                    'on_hold',
                ]),
            ],
            'payment_status' => [
                'required',
                Rule::in(['pending', 'partial', 'paid']),
            ],
            'quoted_amount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'requirements' => ['nullable', 'string', 'max:10000'],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
            'completed_items' => ['nullable', 'array'],
            'completed_items.*' => ['string', 'max:100'],
        ]);

        $completed = $data['completed_items'] ?? [];
        $checklist = json_decode(
            $project->deployment_checklist ?: '[]',
            true
        ) ?: [];

        foreach ($checklist as &$item) {
            $item['done'] = in_array(
                $item['key'],
                $completed,
                true
            );
        }
        unset($item);

        DB::table('website_projects')
            ->where('id', $id)
            ->update([
                'project_status' => $data['project_status'],
                'payment_status' => $data['payment_status'],
                'quoted_amount' => $data['quoted_amount'] ?? null,
                'paid_amount' => $data['paid_amount'] ?? 0,
                'requirements' => $data['requirements'] ?? null,
                'admin_notes' => $data['admin_notes'] ?? null,
                'deployment_checklist' => json_encode(
                    $checklist,
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                ),
                'launched_at' => $data['project_status'] === 'launched'
                    ? ($project->launched_at ?: now())
                    : null,
                'updated_at' => now(),
            ]);

        return back()->with(
            'success',
            'Live website project updated successfully.'
        );
    }
}
