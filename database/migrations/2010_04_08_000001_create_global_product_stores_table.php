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
        Schema::create('global_product_stores', function (Blueprint $table) {
            $table->id();
            $table->unsignedFloat('public_price')->nullable();
            $table->unsignedFloat('cost')->nullable();
            $table->unsignedFloat('min_stock')->nullable();
            $table->unsignedFloat('max_stock')->nullable();
            $table->unsignedFloat('current_stock')->nullable();
            $table->text('description')->nullable();
            $table->boolean('has_inventory_control')->default(true);
            $table->boolean('show_in_online_store')->default(true);
            $table->foreignId('global_product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_product_stores');
    }
};
