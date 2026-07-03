<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'invoice',
        'user_id',
        'user_address_id',
        'subtotal',
        'shipping_cost',
        'total',
        'payment_method',
        'payment_status',
        'order_status',
        'courier',
        'tracking_number',
        'snap_token',
        'transaction_id',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class, 'user_address_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
