<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Month;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function getAllBalanceMonthly()
    {
        $month = Month::create(['month' => 'Jun']);
        $customer = Customer::factory()->create([
            'amount' => 5000,
        ]);

        $subscription = Subscription::create([
            'customer_id' => $customer->id,
            'month_id' => $month->id,
            'amount_paid' => 4000,
        ]);

        $this->assertEquals(1000, $subscription->balance);
        // $this->assertEquals($balance, )
    }
}
