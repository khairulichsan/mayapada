<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // 1. Cukup gunakan guarded kosong agar semua kolom (termasuk is_published) bisa masuk
    protected $guarded = [];

    // 2. Beri tahu Laravel agar membaca angka 1 dan 0 sebagai True dan False
    protected $casts = [
        'is_published' => 'boolean',
        'gallery_images' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }
}
