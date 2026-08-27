<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        $email = $this->authenticatedEmail($request);
        $trials = DB::table('trial_applications')
            ->whereRaw('LOWER(email) = ?', [$email])
            ->latest('created_at')
            ->get([
                'id', 'website_name', 'business_name', 'desired_slug', 'trial_url',
                'template_key', 'status', 'starts_at', 'expires_at', 'upgraded_at',
            ]);

        $projects = DB::table('website_projects')
            ->join('trial_applications', 'website_projects.trial_application_id', '=', 'trial_applications.id')
            ->leftJoin('plans', 'website_projects.plan_id', '=', 'plans.id')
            ->whereRaw('LOWER(trial_applications.email) = ?', [$email])
            ->latest('website_projects.created_at')
            ->get([
                'website_projects.id', 'website_projects.project_name',
                'website_projects.project_type', 'website_projects.custom_domain',
                'website_projects.project_status', 'website_projects.payment_status',
                'website_projects.quoted_amount', 'website_projects.paid_amount',
                'website_projects.deployment_checklist', 'website_projects.created_at',
                'plans.title as plan_title',
            ])->map(function ($project) {
                $items = json_decode($project->deployment_checklist ?: '[]', true) ?: [];
                $project->progress_total = count($items);
                $project->progress_completed = count(array_filter($items, fn ($item) => !empty($item['done'])));
                unset($project->deployment_checklist);
                return $project;
            });

        $latest = $trials->first();

        return response()->json([
            'success' => true,
            'data' => [
                'client' => [
                    'email' => $email,
                    'name' => $latest->owner_name ?? null,
                    'business_name' => $latest->business_name ?? null,
                ],
                'trials' => $trials,
                'projects' => $projects,
            ],
        ]);
    }

    private function authenticatedEmail(Request $request): string
    {
        $token = $request->bearerToken();
        abort_unless($token, 401, 'Authentication token required.');

        $record = DB::table('mobile_client_tokens')
            ->where('token_hash', hash('sha256', $token))
            ->where('expires_at', '>', now())
            ->first();
        abort_unless($record, 401, 'Authentication token is invalid or expired.');

        DB::table('mobile_client_tokens')->where('id', $record->id)->update([
            'last_used_at' => now(), 'updated_at' => now(),
        ]);
        return $record->email;
    }
}
