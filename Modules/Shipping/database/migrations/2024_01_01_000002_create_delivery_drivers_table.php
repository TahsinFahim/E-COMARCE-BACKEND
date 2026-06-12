<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->foreignId('delivery_zone_id')->nullable()->constrained('delivery_zones')->nullOnDelete();
            $table->string('employee_code', 80)->unique();
            $table->string('name', 160);
            $table->string('phone', 40);
            $table->string('email', 160)->nullable();
            $table->string('license_number', 120)->nullable();
            $table->enum('vehicle_type', ['walk', 'bike', 'motorbike', 'car', 'van', 'truck'])->default('motorbike');
            $table->string('vehicle_plate', 80)->nullable();
            $table->decimal('capacity_kg', 8, 2)->nullable();
            $table->enum('status', ['available', 'busy', 'offline', 'inactive'])->default('available');
            $table->decimal('current_latitude', 10, 7)->nullable();
            $table->decimal('current_longitude', 10, 7)->nullable();
            $table->dateTime('last_seen_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['store_id', 'status']);
            $table->index(['delivery_zone_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_drivers');
    }
};
