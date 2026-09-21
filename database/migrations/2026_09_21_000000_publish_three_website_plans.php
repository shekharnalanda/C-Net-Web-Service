<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $now = now();
        $plans = [
            [
                'title' => 'Starter Plan',
                'slug' => 'starter-plan',
                'service_type' => 'Small Business Website',
                'price_label' => '₹4,999',
                'duration' => 'One-time development • 1 year domain & hosting',
                'features' => implode("\n", [
                    'Up to 5 professional pages',
                    'Mobile-responsive design',
                    'Domain and hosting for 1 year',
                    'Free SSL / HTTPS security',
                    'WhatsApp and call buttons',
                    'Contact / enquiry form',
                    'Google Map integration',
                    'Basic SEO setup',
                    '1 business email',
                    '2 design revisions',
                    '15 days free support',
                    'Best for shops, coaching centres, clinics and personal profiles',
                ]),
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'title' => 'Business Plan',
                'slug' => 'business-plan',
                'service_type' => 'Dynamic Business Website',
                'price_label' => '₹11,999',
                'duration' => 'One-time development • 1 year domain & hosting',
                'features' => implode("\n", [
                    'Up to 10 professional pages',
                    'Mobile-responsive premium design',
                    'Domain and hosting for 1 year',
                    'Admin panel for content management',
                    'Photo, gallery and service management',
                    'Contact / enquiry management',
                    'WhatsApp, call and Google Map integration',
                    'Static UPI QR payment option',
                    'Basic SEO and analytics setup',
                    'Google Business Profile assistance',
                    'PWA / website app installer',
                    '2 business emails',
                    '4 design revisions',
                    '3 months free support',
                    'Best for institutions, restaurants, hospitals and growing businesses',
                ]),
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'title' => 'Premium Plan',
                'slug' => 'premium-plan',
                'service_type' => 'Advanced Business Portal',
                'price_label' => '₹24,999 से शुरू',
                'duration' => 'Custom scope • 1 year domain & hosting',
                'features' => implode("\n", [
                    'Up to 20 professional pages',
                    'Advanced mobile-responsive design',
                    'Domain and hosting for 1 year',
                    'Advanced admin panel',
                    'User login and registration',
                    'Product, service or content management',
                    'Payment gateway / UPI integration',
                    'Notifications and business reports',
                    'Advanced SEO and analytics setup',
                    'PWA / website app installer',
                    '5 business emails',
                    '8 design revisions',
                    '6 months free support',
                    'Custom automation as per approved scope',
                    'Suitable for e-commerce, school, news, booking, exam and membership portals',
                ]),
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 30,
            ],
        ];

        DB::table('plans')->whereNotIn('slug', collect($plans)->pluck('slug'))->update([
            'is_active' => false,
            'updated_at' => $now,
        ]);

        foreach ($plans as $plan) {
            DB::table('plans')->updateOrInsert(
                ['slug' => $plan['slug']],
                [...$plan, 'updated_at' => $now, 'created_at' => $now]
            );
        }
    }

    public function down(): void
    {
        DB::table('plans')
            ->whereIn('slug', ['starter-plan', 'business-plan', 'premium-plan'])
            ->update(['is_active' => false, 'updated_at' => now()]);
    }
};
