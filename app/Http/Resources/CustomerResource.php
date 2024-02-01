<?php

namespace App\Http\Resources;

use App\Enums\CustomerStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
    //     data: {
    //   package: {
    //     name: 'FIMNET HOME FIBER BASIC',
    //     speed: 10,
    //     price: '2,000.00',
    //   },
    //   subscriptions: [
    //     {id: 1, month: 'January', paid: true, paymentType: 'mpesa',  amount: '2,500'},
    //     {id: 2, month: 'February', paid: false, paymentType: 'cash',  amount: '2,500'},
    //     {id: 3, month: 'March', paid: false, paymentType: null,  amount: '2,500'},
    //   ]
    // }
        return [
            'name' => $this->name,
            'AccountNumber' => $this->mpesaId,
            'package' => [
                'name' => $this->package->name,
                'price' => number_format($this->package->price, 2),
                'speed' => $this->package->speed,
            ],
            // 'phone_number' => $this->phone_number,
            // 'house' => $this->house->name,
            // 'status' => CustomerStatus::from($this->status)->name,
            // 'avatar' => $this->defaultProfilePhotoUrl(),
            // 'balance' => number_format($this->balance()),
            // 'stats' => [
            //     ['name' => 'Balance', 'value' => number_format($this->balance()), 'unit' => 'KSH'],
            //     ['name' => 'Package', 'value' => $this->package->speed, 'unit' => ''],
            // ],
            // 'unit' => $this->appartment,
            // 'account' => $this->mpesaId,
            // 'due_date' => $this->block_day,
            'subscriptions' => SubscriptionResource::collection($this->subscriptions),
        ];
    }
}
