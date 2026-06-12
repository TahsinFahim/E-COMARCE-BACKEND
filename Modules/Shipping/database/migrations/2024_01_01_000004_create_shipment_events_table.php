<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('delivery_drivers')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('event_type', ['status_update', 'assignment', 'pickup', 'location', 'delivery_attempt', 'exception', 'note'])->default('status_update');
            $table->enum('status', ['pending', 'packed', 'ready_for_pickup', 'out_for_delivery', 'delivered', 'failed', 'returned', 'cancelled'])->nullable();
            $table->string('title', 160);
            $table->string('description', 1000)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->dateTime('occurred_at');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['shipment_id', 'occurred_at']);
            $table->index(['driver_id', 'occurred_at']);
            $table->index(['event_type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_events');
    }
};
