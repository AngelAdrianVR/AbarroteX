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
        Schema::create('discount_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('code'); //código de descuento
            $table->text('description')->nullable(); //descripción del cupón
            $table->boolean('is_percentage_discount')->default(true); //bandera que indica si es descuento en porcentage
            $table->float('discount_amount'); //cantidad de descuento
            $table->unsignedSmallInteger('times_used')->nullable()->default(0); //cantidad de veces que se ha usado.
            $table->boolean('is_active')->default(true); //Bandera que indica si el cupón esta vigente
            $table->timestamp('expired_date')->nullable(); //Fecha para programar expiración del cupón
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_tickets');
    }
};
