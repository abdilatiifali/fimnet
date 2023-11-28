<?php

namespace App\Http\Resources;

use App\Enums\CustomerStatus;
use App\Http\Resources\SubscriptionResource;
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
        return [
            'name' => $this->name,
            'phone_number' => $this->phone_number,
            'house' => $this->house->name,
            'status' => CustomerStatus::from($this->status)->name,
            'avatar' => $this->defaultProfilePhotoUrl(),
            'balance' => number_format($this->balance()),
            'stats' => [
                ['name' => 'Balance','value' => number_format($this->balance()), 'unit' => 'KSH'],
                ['name' => 'Package','value' => $this->package, 'unit' => 'MBPS'],
            ],
            'unit' => $this->appartment,
            'account' => $this->mpesaId,
            'due_date' => $this->block_day,
            'subscriptions' => SubscriptionResource::collection($this->subscriptions),
        ];
    }
}
