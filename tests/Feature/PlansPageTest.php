<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlansPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_three_business_plans_and_expandable_details_are_published(): void
    {
        $this->get('/plans')
            ->assertOk()
            ->assertSee('Starter Plan')
            ->assertSee('₹4,999')
            ->assertSee('Business Plan')
            ->assertSee('₹11,999')
            ->assertSee('MOST POPULAR')
            ->assertSee('Premium Plan')
            ->assertSee('₹24,999 से शुरू')
            ->assertSee('Read More Details')
            ->assertSee('Payment gateway / UPI integration');
    }
}
