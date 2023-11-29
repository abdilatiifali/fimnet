<?php

namespace App\Network;

use App\Enums\CustomerStatus;
use App\Models\Customer;
use App\Models\Router;
use RouterOS\Client;
use RouterOS\Exceptions\BadCredentialsException;
use RouterOS\Exceptions\StreamException;
use RouterOS\Query;

class ApiRouter
{
    protected $router;

    protected $client;

    public function __construct($router)
    {
        $this->router = $router;
    }

    public static function make(Router $router)
    {
        return new self($router);
    }

    public function openServer()
    {
        try {
            $this->client = new Client([
                'host' => $this->router->server,
                'user' => $this->router->user ?? config('services.mikrotik.user'),
                'pass' => config('services.mikrotik.password'),
                'port' => 8728,
            ]);
        } catch (BadCredentialsException $exception) {
            \Log::info('something went wrong');
            throw new \Exception('something went wrong');
        }

        return $this;
    }

    public function disconnectBy($customer)
    {
        $response = $this->blockIpAddress($customer);

        $customer->update([
            'mikrotik_id' => $response['after']['ret'] ?? null,
            'status' => CustomerStatus::blocked->value,
            'blocked_at' => now(),
        ]);

        return;
    }

    public function blockIpAddress($customer)
    {
        $query = (new Query('/ip/firewall/address-list/add'))
                    ->equal('list', config('app.name'))
                    ->equal('address', $customer->ip_address);

        return $this->client->query($query)->read();
    }

    public function disconnect($customers)
    {
        $customers = $customers->lazy();

        $mikrotikIds = [];

        foreach ($customers as $customer) {
             if ($customer->balance <= 0) {
                continue;
             }
            try {
                $response = $this->blockIpAddress($customer);
                $mikrotikIds[$customer->id] = $response['after']['ret'] ?? null;
            } catch (StreamException $e) {
                for ($i = 0; $i < 3; $i++) {
                    sleep(5);
                    try {
                        $response = $this->blockIpAddress($customer);
                        $mikrotikIds[$customer->id] = $response['after']['ret'] ?? null;
                        break; // Request succeeded, break out of the retry loop
                    } catch (StreamException $e) {
                    }
                }
                if (! isset($mikrotikIds[$customer->id])) {
                    continue; // Move on to the next customer
                }
            }
        }

        $this->updateMikrotikIds($mikrotikIds);
        $mikrotikIds = [];
        $this->addFirewallFilterToDropBlockedCustomers();

        return 'done';
    }

    protected function updateMikrotikIds(&$mikrotikIds)
    {
        $updateSql = 'UPDATE customers SET blocked_at = "'.now().'", mikrotik_id = CASE id ';
        foreach ($mikrotikIds as $id => $mikrotikId) {
            $updateSql .= "WHEN {$id} THEN '{$mikrotikId}' ";
        }

        $updateSql .= "END, status = '" . CustomerStatus::blocked->value . "' WHERE id IN (" . implode(',', array_keys($mikrotikIds)) . ")";

        \DB::update($updateSql);

    }

    protected function addFirewallFilterToDropBlockedCustomers()
    {
        $query = (new Query('/ip/firewall/filter/add'))
                    ->equal('chain', 'forward')
                    ->equal('src-address-list', config('app.name'))
                    ->equal('action', 'drop');

        $this->client->query($query)->read();
    }

    public function reconnect($customer)
    {
        $query = (new Query('/ip/firewall/address-list/remove'));
        $query->equal('.id', $customer->mikrotik_id);

        $response = $this->client->query($query)->read();

        $customer->mikrotik_id = null;
        $customer->status = CustomerStatus::active->value;
        $customer->blocked_at = null; 
        $customer->saveQuietly();

        return $response;
    }

    public function queueCustomer($customer)
    {
        return $this->addCustomerToQueue($customer);
    }

    public function getDefaultSpeed()
    {
        return 10;
    }

    public function addCustomerToQueue($customer)
    {
        $customer = $customer->load('house');

        $package = 
            (int) filter_var($customer?->package->speed, FILTER_SANITIZE_NUMBER_INT)
            ?? $this->getDefaultSpeed();

        $item = $this->isIpAddressExsistInTheQueue($customer->ip_address);

        if (! empty($item)) {
            $this->updateCustomer($customer, $item[0]);
            return;
        }

        $query = (new Query('/queue/simple/add'))
                ->equal('name', $customer->mpesaId)
                ->equal('target', $customer->ip_address)
                ->equal('max-limit', "0/${package}M");

        return $this->client->query($query)->read();
    }

    public function isIpAddressExsistInTheQueue($ipAddress)
    {
        return $this->client->query('/queue/simple/print', [
            'target', "{$ipAddress}/32",
        ])->read();
    }

    public function updateQueue($customer)
    {
        $item = $this->isIpAddressExsistInTheQueue($customer->getOriginal()['ip_address']);

        if (! empty($item)) {
            $this->updateCustomer($customer, $item[0]);

            return;
        }

        return $this->addCustomerToQueue($customer);
    }

    public function updateCustomer($customer, $item)
    {
        $package = 
            (int) filter_var($customer?->package->speed, FILTER_SANITIZE_NUMBER_INT)
            ?? $this->getDefaultSpeed();

        return $this->client->query(
            (new Query('/queue/simple/set'))
                ->equal('.id', $item['.id'])
                ->equal('name', $customer->mpesaId)
                ->equal('target', $customer->ip_address)
                ->equal('max-limit', "0/${package}M")
        )->read();
    }
}
