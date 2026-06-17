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
    Schema::create('orders', function (Blueprint $table) {
        $table->string('id')->primary(); // Contoh: ORD-20260611-001
        $table->foreignId('customer_id')->constrained('users');
        $table->string('customer_name');
        $table->string('customer_phone');
        $table->text('shipping_address');

        // Integrasi RajaOngkir & Logistik
        $table->string('shipping_courier'); // jne, pos, jnt
        $table->string('shipping_service')->nullable(); // REG, Oke, dll.
        $table->decimal('shipping_cost', 10, 2);
        $table->string('tracking_number')->nullable(); // Nomor Resi dari Admin

        // Kalkulasi Finansial
        $table->decimal('subtotal', 12, 2);
        $table->decimal('total_price', 12, 2); // Subtotal + Ongkir

        // Status Otomatis Payment Gateway (Midtrans)
        $table->string('payment_status')->default('pending'); // pending, paid, failed, expired
        $table->string('shipping_status')->default('pending'); // pending, processing, shipped, completed
        $table->string('midtrans_snap_token')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
