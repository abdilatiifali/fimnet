<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TicketEnum;

class Ticket extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function closeTicket()
    {
        $ticket = $this->update([
            'status' => TicketEnum::closed->value
        ]);
       
        SmsGateway::solveComplain($this->customer);

        return $ticket;
    }
}
