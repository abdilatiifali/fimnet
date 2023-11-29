<?php

namespace App\Providers;

use App\Enums\CustomerStatus;
use App\Listeners\SendCustomerCrediantials;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\Transaction;
use App\Network\ApiRouter;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        CustomerSubscriptionUpdated::class => [
            UpdateSubscription::class,
        ],
        CustomerCreated::class => [
            QueueCustomer::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Quotation::creating(function ($pivot) {
            $subscription = Subscription::where('customer_id', $pivot->customer_id)
                ->where('month_id', now()->month)
                ->where('session_id', config('app.year'))
                ->first();

            $totalAmount = 0;
            foreach ($pivot->line_items as $item) {
                $totalAmount += $item['amount'] * $item['quantity'];
            }

            $subscription->update(['amount' => $totalAmount]);
        });

        Quotation::updating(function ($pivot) {
            $subscription = Subscription::where('customer_id', $pivot->customer_id)
                ->where('month_id', now()->month)
                ->where('session_id', config('app.year'))
                ->first();

            $totalAmount = 0;
            foreach ($pivot->line_items as $item) {
                $totalAmount += $item['amount'] * $item['quantity'];
            }

            $subscription->update(['amount' => $totalAmount]);
        });


        Pivot::creating(function ($pivot) {
            if ($pivot->amount_paid >= $pivot->amount && now()->month == $pivot->month_id) {
                $customer = Customer::findOrFail($pivot->customer_id);
                $customer->status = CustomerStatus::active->value;
                $customer->saveQuietly();
            }
        });

        Pivot::updating(function ($pivot) {
            $pivot->balance = $pivot->amount - $pivot->amount_paid;

            if ($pivot->amount_paid >= $pivot->amount && now()->month == $pivot->month_id) {    
               event(new CustomerSubscriptionUpdated($pivot));
               return;
            }

            return;
            
        });

        Customer::creating(function ($customer) {
            $customer->amount = $customer->package()->first()->price;
            event(new CustomerCreated($customer));
        });

        Customer::updating(function ($customer) {
            $customer->amount = $customer->package()->first()->price;
            if (! $customer->router || ! filter_var($customer->ip_address, FILTER_VALIDATE_IP)) {
                return;
            }

            ApiRouter::make($customer->router)
                ->openServer()
                ->updateQueue($customer);
        });

    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
