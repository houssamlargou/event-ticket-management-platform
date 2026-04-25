<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'ticket_id',
        'quantity',
        'total_price'
    ];

    public function user(){
        return $this->BelongsTo(User::class);
    }

    public function Ticket(){
        return $this->BelongsTo(Ticket::class);
    }
}
