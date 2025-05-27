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
            $table->string('product_name');
            $table->unsignedMediumInteger('product_id')->nullable();
            $table->boolean('is_global_product');
            $table->unsignedFloat('original_price'); //indica el precio que se cambió para esa venta solamente
            $table->unsignedFloat('quantity');
            $table->timestamp('refunded_at')->nullable();
            $table->string('folio');
            $table->string('payment_method')->nullable(); //tipo de pago, puede ser efectivo, tarjeta, etc.
            $table->foreignId('client_id')->nullable()->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('sales');
    }
};
