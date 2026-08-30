<?php

namespace App\Http\Controllers;

use App\Services\AdminNotificationService;
use App\Services\CentralSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnquiryController extends Controller
{
    public function create()
    {
        return view('enquiry');
    }

    public function store(Request $request, CentralSyncService $centralSync)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'service' => ['required', 'string', 'max:100'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $enquiryId = DB::table('enquiries')->insertGetId([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'service' => $data['service'],
            'message' => $data['message'] ?? null,
            'status' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $centralSync->enquiry([
            'business_code' => config('services.mci_central.business_code'),
            'source_reference_id' => 'web-enquiry-'.$enquiryId,
            'source_site' => config('app.url', 'https://web.mciedu.com'),
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'subject' => 'C-Net Web Services Enquiry',
            'message' => $data['message'] ?: 'Service enquiry for '.$data['service'],
            'category' => 'general',
            'course_service' => $data['service'],
        ]);

        AdminNotificationService::sendEnquiry($data);

        return back()->with('success', 'Thank you! Your enquiry has been submitted successfully.');
    }
}
