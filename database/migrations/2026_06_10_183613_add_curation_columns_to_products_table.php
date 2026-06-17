<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Menambah modal grosir & satuan dari Supplier
            $table->integer('wholesale_price')->nullable()->after('category_id');
            $table->string('wholesale_unit')->default('lusin')->after('wholesale_price'); // contoh: lusin, kodi, bal

            // Menambah saklar kontrol Admin (default: false / disembunyikan dari web)
            $table->boolean('is_published')->default(false)->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['wholesale_price', 'wholesale_unit', 'is_published']);
        });
    }
};
