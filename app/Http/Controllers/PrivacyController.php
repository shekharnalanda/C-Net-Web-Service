<?php

namespace App\Http\Controllers;

use App\Services\AdminNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PrivacyController extends Controller
{
    public function policy(): View
    {
        return view('legal.privacy');
    }

    public function deletionForm(): View
    {
        return view('legal.account-deletion');
    }

    public function requestDeletion(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'confirmation' => ['accepted'],
        ]);

        $message = 'Account/data deletion request submitted from the C-Net Web Services app compliance page.';
        if (! empty($data['reason'])) {
            $message .= "\nReason: ".$data['reason'];
        }

        $enquiry = [
            'name' => $data['name'],
            'phone' => $data['phone'] ?? 'Not provided',
            'email' => strtolower(trim($data['email'])),
            'service' => 'Account & Data Deletion',
            'message' => $message,
        ];

        DB::table('enquiries')->insert($enquiry + [
            'status' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        AdminNotificationService::sendEnquiry($enquiry);

        return back()->with('success', 'Your deletion request has been recorded. C-Net Web Services will verify it by email and process it within 30 days.');
    }
}
