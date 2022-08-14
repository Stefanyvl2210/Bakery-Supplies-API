<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    use HasFactory;

    protected $table = "orders";

    protected $fillable = [
        'is_guest', 'guest_data', 'guest_data', 'status', 'delivery_type', 'total', 'user_id', 'payment_id', 'address_id',
    ];

    public function user() {
        return $this->belongsTo( User::class );
    }

}
