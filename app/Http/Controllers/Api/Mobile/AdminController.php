<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'string', 'max:255'],
            'device_name' => ['nullable', 'string', 'max:150'],
        ]);

        $user = User::query()->where('email', Str::lower(trim($data['email'])))->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Admin email or password is incorrect.',
            ]);
        }

        $plainToken = Str::random(80);
        DB::table('mobile_admin_tokens')->insert([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $plainToken),
            'device_name' => $data['device_name'] ?? 'C-Net Web Services Admin App',
            'expires_at' => now()->addDays(14),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'token' => $plainToken,
            'token_type' => 'Bearer',
            'expires_in' => 1209600,
            'admin' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
        ]);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $user = $this->admin($request);

        $counts = [
            'enquiries' => DB::table('enquiries')->count(),
            'new_enquiries' => DB::table('enquiries')->where('status', 'new')->count(),
            'trials' => DB::table('trial_applications')->count(),
            'active_trials' => DB::table('trial_applications')->where('status', 'approved')->count(),
            'projects' => DB::table('website_projects')->count(),
            'active_projects' => DB::table('website_projects')
                ->whereNotIn('project_status', ['launched', 'on_hold'])->count(),
        ];

        $enquiries = DB::table('enquiries')->latest('created_at')->limit(20)->get();
        $trials = DB::table('trial_applications')->latest('created_at')->limit(20)->get([
            'id', 'website_name', 'business_name', 'owner_name', 'email', 'phone',
            'trial_url', 'template_key', 'status', 'expires_at', 'created_at',
        ]);
        $projects = DB::table('website_projects')
            ->leftJoin('trial_applications', 'website_projects.trial_application_id', '=', 'trial_applications.id')
            ->latest('website_projects.created_at')->limit(20)->get([
                'website_projects.id', 'website_projects.project_name',
                'website_projects.custom_domain', 'website_projects.project_status',
                'website_projects.payment_status', 'website_projects.updated_at',
                'trial_applications.owner_name', 'trial_applications.email',
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'admin' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
                'counts' => $counts,
                'enquiries' => $enquiries,
                'trials' => $trials,
                'projects' => $projects,
            ],
        ]);
    }

    public function updateEnquiry(Request $request, int $id): JsonResponse
    {
        $this->admin($request);
        $data = $request->validate([
            'status' => ['required', 'in:new,contacted,in_progress,completed,closed'],
        ]);
        abort_unless(DB::table('enquiries')->where('id', $id)->exists(), 404);
        DB::table('enquiries')->where('id', $id)->update([
            'status' => $data['status'], 'updated_at' => now(),
        ]);
        return response()->json(['success' => true, 'message' => 'Enquiry status updated.']);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        if ($token) {
            DB::table('mobile_admin_tokens')->where('token_hash', hash('sha256', $token))->delete();
        }
        return response()->json(['success' => true, 'message' => 'Admin signed out.']);
    }

    private function admin(Request $request): User
    {
        $token = $request->bearerToken();
        abort_unless($token, 401, 'Admin authentication required.');

        $record = DB::table('mobile_admin_tokens')
            ->where('token_hash', hash('sha256', $token))
            ->where('expires_at', '>', now())->first();
        abort_unless($record, 401, 'Admin token is invalid or expired.');

        $user = User::query()->find($record->user_id);
        abort_unless($user, 401, 'Admin account was not found.');

        DB::table('mobile_admin_tokens')->where('id', $record->id)->update([
            'last_used_at' => now(), 'updated_at' => now(),
        ]);
        return $user;
    }
}
