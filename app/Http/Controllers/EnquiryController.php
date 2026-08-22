<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AdminNotificationService;

class EnquiryController extends Controller
{
    public function create()
    {
        return view('enquiry');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'service' => ['required', 'string', 'max:100'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::table('enquiries')->insert([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'service' => $data['service'],
            'message' => $data['message'] ?? null,
            'status' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        AdminNotificationService::sendEnquiry($data);

        return back()->with('success', 'Thank you! Your enquiry has been submitted successfully.');
    }
}
