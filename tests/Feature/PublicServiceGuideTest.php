<?php

namespace Tests\Feature;

use Database\Seeders\PublicServiceGuideSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PublicServiceGuideTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PublicServiceGuideSeeder::class);
    }

    public function test_homepage_lists_nine_main_services_and_explains_the_client_process(): void
    {
        $response = $this->get('https://web.mciedu.com/')->assertOk()
            ->assertSee('C-Net Web Services क्या है?')->assertSee('काम कैसे होगा?')
            ->assertSee('Training')->assertSee('CRM')->assertSee('School ERP')
            ->assertSee('Domain/Hosting Renewal')->assertSee('शुरुआती Trial अवधि 7 दिन है।');
        foreach (config('service-guide.services') as $slug => $guide) {
            $response->assertSee('https://web.mciedu.com/services/'.$slug, false);
        }
        $this->assertSame(9, substr_count($response->getContent(), '<article class="service-card">'));
    }

    public function test_all_services_have_detailed_public_pages_and_service_specific_enquiry_links(): void
    {
        foreach (config('service-guide.services') as $slug => $guide) {
            $response = $this->get('/services/'.$slug)->assertOk()
                ->assertSee($guide['label'])->assertSee($guide['intro'])
                ->assertSee('आपसे क्या जानकारी चाहिए?')->assertSee($guide['example'])
                ->assertSee('service='.$slug, false)->assertSee('सीधे होम पेज पर लौटें');
            foreach ($guide['sections'] as $section) {
                $response->assertSee($section['title'])->assertSee($section['text']);
            }
        }
    }

    public function test_catalog_updates_preserve_existing_ids_inactive_choices_and_unrelated_services(): void
    {
        $id = DB::table('services')->where('slug', 'crm-management')->value('id');
        DB::table('services')->where('id', $id)->update(['is_active' => false]);
        DB::table('services')->insert(['slug' => 'existing-custom-service', 'title' => 'Existing Custom', 'short_description' => 'Custom summary', 'description' => 'Custom description', 'is_active' => true, 'sort_order' => 90]);
        $this->seed(PublicServiceGuideSeeder::class);
        $this->assertDatabaseHas('services', ['id' => $id, 'slug' => 'crm-management', 'is_active' => false]);
        $this->assertDatabaseHas('services', ['slug' => 'existing-custom-service', 'description' => 'Custom description']);
        $this->assertSame(12, DB::table('services')->count());
        $this->get('/services/crm-management')->assertNotFound();
        $this->get('/')->assertOk()->assertDontSee('href="http://localhost/services/crm-management"', false);
        $this->get('/services/existing-custom-service')->assertOk()->assertSee('Custom description');
    }

    public function test_enquiry_lists_new_services_and_safely_preselects_the_requested_service(): void
    {
        $this->get('/enquiry?service=school-erp')->assertOk()
            ->assertSee('School ERP &amp; Institute Management" selected', false)
            ->assertSee('CRM &amp; Lead Management', false)->assertSee('Enquiry भेजें');
        $this->withSession(['_old_input' => ['service' => 'Other Requirement']])
            ->get('/enquiry?service=school-erp')->assertOk()
            ->assertSee('value="Other Requirement" selected', false)
            ->assertDontSee('School ERP &amp; Institute Management" selected', false);
    }

    public function test_unknown_services_are_404_and_admin_service_copy_is_escaped(): void
    {
        $this->get('/services/unknown-service')->assertNotFound();
        DB::table('services')->where('slug', 'school-erp')->update(['description' => '<script>unsafeService()</script>']);
        $this->get('/services/school-erp')->assertOk()
            ->assertDontSee('<script>unsafeService()</script>', false)
            ->assertSee('&lt;script&gt;unsafeService()&lt;/script&gt;', false);
        $this->get('https://not-an-active-trial.mciedu.com/')->assertNotFound();
    }
}
