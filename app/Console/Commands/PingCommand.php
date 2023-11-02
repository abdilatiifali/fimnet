<?php

namespace App\Console\Commands;

use App\Models\Router;
use App\Models\SmsGateway;
use Illuminate\Console\Command;

class PingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ping-routers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ping Servers after every 2 Minute to see if a link is up or down again';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->ping();
    }

    public function ping()
    {
        $password = 'abdi';

        $hosts = [
            'Town' => '102.214.84.84',
            'Big Router' => '102.214.84.2', 
            'Small Router' => '102.214.84.3', 
            'Testing Router' => '102.214.84.4', 
            'Al Mukarama Router' => '102.214.84.5', 
            'Daus Router' => '102.214.84.99', 
            'Kalimani Router' => '102.214.84.66',
            'Adizon Router' => '102.214.84.34',
            'City Park' => '102.214.84.83',
        ];

        foreach($hosts as $key => $host) {
            $command = "echo $password | sudo -S /sbin/ping -c 5 -W 5 {$host}";
            exec($command, $output, $result);

             if ($result == 0) {
                if (\Cache::has("{$key}_down")) {
                    SmsGateway::routerIsUp($key);
                    \Cache::forget("{$host}_down");
                }
                preg_match('/round-trip min\/avg\/max\/stddev = ([0-9\.]+)\/([0-9\.]+)\/([0-9\.]+)\/([0-9\.]+) ms/', implode("\n", $output), $matches);
                $avg_time = isset($matches[2]) ? $matches[2] : null;
                if ($avg_time !== null) {
                    printf("%s is reachable, average round-trip time is %.2f ms\n", $host, $avg_time);
                } else {
                    printf("%s is reachable, but could not get average round-trip time\n", $host);
                }
            } else {
                printf("%s is unreachable\n", $host);
                if (! \Cache::has("{$key}_down")) {
                    SmsGateway::routerIsDown($key);
                    \Cache::forever("{$key}_down", true);
                }
            }
        }
    }
}
