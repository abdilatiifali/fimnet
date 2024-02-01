<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'id' => $this->id,
            'month' => $this->month,
            'amount' => $this->pivot->amount,
            'paid' => $this->pivot->paid,
            'paymentType' => $this->pivot->paid ? $this->pivot->payment_type : null,
        ];
    }
}
