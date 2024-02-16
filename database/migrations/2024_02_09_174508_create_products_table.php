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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedFloat('public_price');
            $table->unsignedFloat('cost')->nullable();
            $table->string('code')->unique()->nullable();
            $table->unsignedSmallInteger('min_stock')->nullable();
            $table->unsignedSmallInteger('max_stock')->nullable();
            $table->unsignedFloat('current_stock')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
