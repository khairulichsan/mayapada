<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestockOrder extends Model
{
    // 1. Beritahu Laravel bahwa ID kita berbentuk string (RST-...), bukan angka auto-increment
    protected $keyType = 'string';
    public $incrementing = false;

    // 2. Buka gembok keamanan agar Controller bisa menyimpan data PO ke semua kolom
    protected $guarded = [];

    // Relasi ke tabel Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi ke tabel User (sebagai Supplier)
    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }
}
