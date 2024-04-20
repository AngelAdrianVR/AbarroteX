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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedFloat('current_price');
            $table->unsignedFloat('quantity');
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->morphs('saleable');
            // $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete(); // en caso de vender un producto local
            // $table->foreignId('global_product_store_id')->nullable()->constrained()->cascadeOnDelete(); // en caso de vender un producto transferido del catálogo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
