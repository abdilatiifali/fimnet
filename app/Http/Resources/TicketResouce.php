<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\TicketEnum;

class TicketResouce extends JsonResource
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
            'title' => $this->title,
            'createdAt' => $this->created_at->diffForHumans(),
            'customer' => $this->customer->name,
            'status' => TicketEnum::from($this->status)->name,
        ];
    }
}
