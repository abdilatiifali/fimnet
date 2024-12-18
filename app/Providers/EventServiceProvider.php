<?php

namespace App\Providers;

use App\Enums\CustomerStatus;
use App\Listeners\CheckIfTheIpIsNated;
use App\Models\Customer;
use App\Models\Income;
use App\Models\Quotation;
use App\Models\Subscription;
use App\Network\ApiRouter;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Enums\PaymentType;
use App\Models\Ticket;
use App\Models\SmsGateway;

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
            CheckIfTheIpIsNated::class,
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

        Ticket::creating(function ($pivot) {
            $customer = Customer::find($pivot->customer_id);

            if (!$customer) return;

            SmsGateway::sendComplain($customer, $pivot);

        });


        Quotation::creating(function ($pivot) {
            $customer = Customer::findOrFail($pivot->customer_id);
            $subscription = Subscription::where('customer_id', $pivot->customer_id)
                ->where('month_id', now()->month)
                ->where('session_id', config('app.year'))
                ->first();

            $totalAmount = 0;
            foreach ($pivot->line_items as $item) {
                $totalAmount += $item['amount'] * $item['quantity'];
            }

            $subscription->update([
                'amount' => $totalAmount + $subscription->amount,
            ]);

            $newPivot = $subscription->fresh();

            $newPivot->update([
                'balance' => $newPivot->amount - $newPivot->amount_paid
            ]);

        });

        Quotation::updating(function ($pivot) {
            $customer = Customer::findOrFail($pivot->customer_id);
            $subscription = Subscription::where('customer_id', $pivot->customer_id)
                ->where('month_id', now()->month)
                ->where('session_id', config('app.year'))
                ->first();

            $totalAmount = 0;
            foreach ($pivot->line_items as $item) {
                $totalAmount += $item['amount'] * $item['quantity'];
            }

            $subscription->update([
                'amount' => $totalAmount + $customer->amount,
            ]);

            $newPivot = $subscription->fresh();

            $newPivot->update([
                'balance' => $newPivot->amount - $newPivot->amount_paid
            ]);
        });

        Pivot::creating(function ($pivot) {
            if (now()->month == $pivot->month_id && $pivot->amount_paid >= $pivot->amount) {
                event(new CustomerSubscriptionUpdated($pivot));
            }
        });

        Pivot::updating(function ($pivot) {
            $pivot->balance = $pivot->amount - $pivot->amount_paid;
            if ($pivot->amount_paid >= $pivot->amount && now()->month == $pivot->month_id) {
                event(new CustomerSubscriptionUpdated($pivot));

                return;
            }

        });

        Customer::creating(function ($customer) {
            $customer->amount = $customer?->package?->price;
            event(new CustomerCreated($customer));
        });

        Customer::updating(function ($customer) {
            $customer->amount = $customer->package->price;

            if (! $customer->router || ! filter_var($customer->ip_address, FILTER_VALIDATE_IP)) {
                return;
            }

            ApiRouter::make($customer->router)
                ->openServer()
                ->updateQueue($customer);

            ApiRouter::make($customer->router)
                    ->openServer()
                    ->checkIfTheIpIsNated($customer);
                    
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
