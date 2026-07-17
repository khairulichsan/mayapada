<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_requests', function (Blueprint $table) {
            $table->id();
            // Data Request dari Admin
            $table->string('request_title');
            $table->string('size');
            $table->string('color');
            $table->integer('qty_requested');

            // Relasi ke Supplier
            $table->foreignId('target_supplier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('responded_by_id')->nullable()->constrained('users')->nullOnDelete();

            // Penawaran Balik dari Supplier
            $table->decimal('offered_price', 15, 2)->nullable();
            $table->date('estimated_delivery')->nullable();

            // Status: pending, supplier_bid, po_created, rejected
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_requests');
    }
};
