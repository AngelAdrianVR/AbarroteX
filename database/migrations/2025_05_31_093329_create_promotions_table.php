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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->morphs('promotionable'); // producto local o global a aplicar la promoción
            $table->string('type');
            // para tipo fijo o porcentaje
            $table->unsignedFloat('discounted_price')->nullable();
            $table->unsignedFloat('discount')->nullable();
            // para tipo precio especial por paquete
            $table->unsignedFloat('pack_quantity')->nullable();
            $table->unsignedFloat('pack_price')->nullable();
            // para tipo compra y regala
            $table->morphs('giftable'); // producto local o global a regalar
            $table->unsignedFloat('min_quantity_to_gift')->nullable();
            $table->unsignedFloat('quantity_to_gift')->nullable();
            // para tipo 2x1 o 3x2
            $table->unsignedFloat('buy_quantity')->nullable();
            $table->unsignedFloat('pay_quantity')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('expiration_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
