<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Buka gembok keamanan agar nama kategori baru bisa tersimpan
    protected $guarded = [];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
