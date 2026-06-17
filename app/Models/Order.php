<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // Karena ID order kita berbentuk string (cth: ORD-20260611-001), matikan auto-increment
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'customer_id', 'customer_name', 'customer_phone',
        'shipping_address', 'shipping_courier', 'shipping_service',
        'shipping_cost', 'tracking_number', 'subtotal',
        'total_price', 'payment_status', 'shipping_status', 'midtrans_snap_token'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
