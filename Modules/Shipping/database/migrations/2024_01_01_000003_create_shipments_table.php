<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->foreignId('delivery_zone_id')->nullable()->constrained('delivery_zones')->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('delivery_drivers')->nullOnDelete();
            $table->foreignId('shipping_address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->string('tracking_number', 80)->unique();
            $table->string('carrier_name', 120)->nullable();
            $table->enum('service_level', ['standard', 'express', 'same_day', 'pickup'])->default('standard');
            $table->enum('delivery_type', ['home_delivery', 'store_pickup', 'third_party'])->default('home_delivery');
            $table->enum('status', ['pending', 'packed', 'ready_for_pickup', 'out_for_delivery', 'delivered', 'failed', 'returned', 'cancelled'])->default('pending');
            $table->decimal('shipping_cost', 19, 4)->default(0);
            $table->decimal('package_weight_kg', 8, 2)->nullable();
            $table->unsignedSmallInteger('package_count')->default(1);
            $table->string('recipient_name', 160)->nullable();
            $table->string('recipient_phone', 40)->nullable();
            $table->string('delivery_instructions', 1000)->nullable();
            $table->date('scheduled_delivery_date')->nullable();
            $table->dateTime('eta_at')->nullable();
            $table->dateTime('shipped_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['order_id']);
            $table->index(['store_id', 'status', 'created_at']);
            $table->index(['delivery_zone_id', 'status']);
            $table->index(['driver_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
