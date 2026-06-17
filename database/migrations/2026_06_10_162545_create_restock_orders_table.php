<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('restock_orders', function (Blueprint $table) {
        $table->string('id')->primary(); // Contoh: RST-20260611-999
        $table->foreignId('supplier_id')->constrained('users');
        $table->string('supplier_brand');
        $table->foreignId('product_id')->constrained('products');
        $table->string('sku');
        $table->integer('qty');
        $table->decimal('price', 12, 2); // Harga kulakan gudang
        $table->string('status')->default('pending'); // pending, approved, rejected
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restock_orders');
    }
};
