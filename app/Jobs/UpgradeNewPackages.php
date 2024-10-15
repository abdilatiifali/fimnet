<?php

namespace App\Jobs;

use App\Models\Router;
use App\Network\ApiRouter;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class UpgradeNewPackages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $customers, public $routerId, public $batchSize = 100, public $offset = 0)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $batch = collect($this->customers)->slice($this->offset, $this->batchSize);
            ApiRouter::make(Router::findOrFail($this->routerId))
                ->openServer()
                ->queueBatch($batch);
        } catch (\Throwable $e) {
            $this->fail($e);
        }
    }
}
