<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $services = DB::table('services')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->limit(8)
            ->get(['id', 'title', 'slug', 'icon', 'short_description']);

        $plans = DB::table('plans')
            ->where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit(6)
            ->get(['id', 'title', 'slug', 'service_type', 'price_label', 'duration', 'features', 'is_featured']);

        return response()->json([
            'success' => true,
            'data' => [
                'brand' => [
                    'name' => 'C-Net Web Services',
                    'tagline' => 'Complete Website Solutions',
                    'website' => 'https://web.mciedu.com',
                    'trial_url' => 'https://web.mciedu.com/trial/apply',
                    'email' => 'cnetbiharsharif@gmail.com',
                    'phone' => '7782801846',
                ],
                'services' => $services,
                'plans' => $plans,
                'features' => [
                    'Domain Registration',
                    'Web Hosting',
                    'Static & Dynamic Websites',
                    'Trial Website Builder',
                    'SEO & Certification',
                    'Digital Promotion',
                ],
            ],
        ]);
    }

    public function services(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => DB::table('services')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('title')
                ->get(['id', 'title', 'slug', 'icon', 'short_description', 'description']),
        ]);
    }

    public function plans(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => DB::table('plans')
                ->where('is_active', true)
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->get(['id', 'title', 'slug', 'service_type', 'price_label', 'duration', 'features', 'is_featured']),
        ]);
    }

    public function health(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'service' => 'C-Net Web Services Mobile API',
            'version' => 'v1',
            'time' => now()->toIso8601String(),
        ]);
    }
}
