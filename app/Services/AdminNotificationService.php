<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AdminNotificationService
{
    public static function sendEnquiry(array $data): void
    {
        $message = implode("\n", [
            'A new enquiry has been received on C-Net Web Services.',
            '',
            'Name: '.$data['name'],
            'Phone: '.$data['phone'],
            'Email: '.($data['email'] ?? 'Not provided'),
            'Service: '.$data['service'],
            'Message: '.($data['message'] ?? 'Not provided'),
            '',
            'Admin: https://web.mciedu.com/admin/dashboard',
        ]);

        self::send('New C-Net Web Services Enquiry', $message);
    }

    public static function sendTrialApplication(array $data): void
    {
        $message = implode("\n", [
            'A new trial website application has been received.',
            '',
            'Business: '.$data['business_name'],
            'Owner: '.$data['owner_name'],
            'Phone: '.$data['phone'],
            'Email: '.($data['email'] ?? 'Not provided'),
            'Category: '.$data['category'],
            'Requested URL: https://'.$data['desired_slug'].'.mciedu.com',
            '',
            'Admin: https://web.mciedu.com/admin/trials',
        ]);

        self::send('New Trial Website Application', $message);
    }

    private static function send(string $subject, string $message): void
    {
        try {
            Mail::raw($message, function ($mail) use ($subject): void {
                $mail->to(config('cnet.admin_notification_email'))
                    ->subject($subject);
            });
        } catch (Throwable $exception) {
            Log::error('Admin notification email failed', [
                'subject' => $subject,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
