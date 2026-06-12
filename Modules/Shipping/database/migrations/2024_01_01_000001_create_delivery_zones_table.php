<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->string('name', 160);
            $table->string('code', 60)->unique();
            $table->string('city', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->unsignedSmallInteger('country_id')->nullable();
            $table->json('postal_codes')->nullable();
            $table->decimal('base_fee', 19, 4)->default(0);
            $table->decimal('per_km_fee', 19, 4)->default(0);
            $table->decimal('free_shipping_min', 19, 4)->nullable();
            $table->decimal('max_delivery_distance_km', 8, 2)->nullable();
            $table->unsignedTinyInteger('estimated_min_days')->default(1);
            $table->unsignedTinyInteger('estimated_max_days')->default(3);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['store_id', 'status']);
            $table->index(['city', 'state']);
            $table->foreign('country_id')->references('id')->on('countries')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_zones');
    }
};
