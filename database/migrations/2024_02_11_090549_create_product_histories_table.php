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
        Schema::create('product_histories', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('type');
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete(); // en caso de vender un producto local
            $table->foreignId('global_product_store_id')->nullable()->constrained()->cascadeOnDelete(); // en caso de vender un producto transferido del catálogo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_histories');
    }
};
