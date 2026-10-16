<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PublicServiceGuideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            foreach (config('service-guide.services') as $slug => $guide) {
                $existing = DB::table('services')->where('slug', $slug)->first();
                $description = $guide['intro'];
                $data = [
                    'title' => $guide['title'], 'icon' => $guide['icon'],
                    'short_description' => $guide['short'], 'description' => $description,
                    'sort_order' => array_search($slug, array_keys(config('service-guide.services')), true) + 1,
                    'updated_at' => now(),
                ];
                if ($existing) {
                    DB::table('services')->where('id', $existing->id)->update($data);
                } else {
                    DB::table('services')->insert($data + ['slug' => $slug, 'is_active' => true, 'created_at' => now()]);
                }
            }
        });
    }
}
