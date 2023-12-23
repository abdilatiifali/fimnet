<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

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
            $pivot->session_id = session('year');
            $pivot->balance = $pivot->amount - $pivot->amount_paid;
        });
    }
}
