<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use App\Enums\PaymentType;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();

        $session = \DB::table('sessions')
            ->where('year', now()->year)->first();

        if (!$session) return;
        
        if (! session()->has('year')) {
            session()->put('year', $session->id);
        }

        Pivot::creating(function ($pivot) {
            $this->validatePayment($pivot);

            $pivot->session_id = session('year');
            $pivot->balance = $pivot->amount - $pivot->amount_paid;
        });

        Pivot::updating(function ($pivot) {
            $this->validatePayment($pivot);
        });
    }

    public function validatePayment($pivot)
    {
        if ($pivot->paid  && $pivot->payment_type == PaymentType::_->value) {
            throw new \Exception('You can only to select Cash as payment Type.');
        }

        if ($pivot->paid  && $pivot->payment_type == PaymentType::mpesa->value) {
            if (! auth()->user()->isSuperAdmin()) {
                throw new \Exception('You can only to select Cash as payment Type.');
            }
        }

        if ($pivot->amount_paid && ! $pivot->paid) {
            throw new \Exception('Make sure to tick the paid option');
        }

    }
}
