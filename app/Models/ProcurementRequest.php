<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_title',
        'size',
        'color',
        'qty_requested',
        'target_supplier_id',
        'responded_by_id',
        'offered_price',
        'estimated_delivery',
        'status',
    ];

    // Relasi untuk menarik data nama Supplier yang membalas (Responder)
    public function responder()
    {
        return $this->belongsTo(User::class, 'responded_by_id');
    }

    // Relasi untuk target Supplier spesifik
    public function targetSupplier()
    {
        return $this->belongsTo(User::class, 'target_supplier_id');
    }
}
