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
        Schema::create('cash_cuts', function (Blueprint $table) {
            $table->id();
            $table->unsignedFloat('started_cash');
            $table->unsignedFloat('expected_cash'); //suma algebraica de ingresos y retiros de caja
            $table->unsignedFloat('store_sales_cash')->nullable(); //Total de venta en tienda pagado en efectivo
            $table->unsignedFloat('store_sales_card')->nullable(); // Total de venta en tienda pagado con tarjeta
            $table->unsignedFloat('online_sales_cash')->nullable(); // Total de venta en línea pagado en efectivo
            $table->unsignedFloat('online_sales_card')->nullable(); // Total de venta en línea pagado con tarjeta
            $table->unsignedFloat('counted_cash'); //dinero contado manualmente de la caja
            $table->unsignedFloat('withdrawn_cash')->nullable(); //dinero retirado de caja despues de hacer el corte
            $table->float('difference'); //diferencia entre el dinero esperado y el contado manualmente
            $table->string('notes')->nullable();
            $table->foreignId('cash_register_id')->constrained()->cascadeOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_cuts');
    }
};
