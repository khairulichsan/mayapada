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
    Schema::create('order_items', function (Blueprint $table) {
        $table->id();
        $table->string('order_id');
        $table->foreignId('product_id')->constrained('products');
        $table->foreignId('variant_id')->nullable()->constrained('product_variants');
        $table->string('sku');
        $table->string('name');
        $table->string('variant_name')->nullable();
        $table->decimal('price', 12, 2);
        $table->integer('qty');
        $table->decimal('subtotal', 12, 2);
        $table->timestamps();

        $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
