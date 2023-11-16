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
            'customerId' => $this->pivot->customer_id,
            'amount' => $this->pivot->amount,
            'amount_paid' => $this->pivot->amount_paid,
            'status' => $this->pivot->paid ? 'paid' : 'Incomplete',
            'date' => $this->pivot->paid ? '45 minutes ago' : '',
            'color' => $this->pivot->paid ? 'text-green-400 bg-green-400/10' : 'text-rose-400 bg-rose-400/10',
            'dateTime' => $this->created_at,
        ];
    }
}
