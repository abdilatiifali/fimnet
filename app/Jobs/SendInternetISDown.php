<?php

namespace App\Jobs;

use App\Models\SmsGateway;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendInternetISDown implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $customers, public $batchSize = 10, public $offset = 0)
    {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $batch = collect($this->customers)->slice($this->offset, $this->batchSize);

        foreach ($batch as $customer) {
            if ($customer->phone_number !== '0') {
                SmsGateway::sendInternetIsDownMessage($customer);
            }
        }
    }
}
