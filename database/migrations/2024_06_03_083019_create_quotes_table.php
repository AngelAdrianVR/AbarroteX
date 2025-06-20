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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->nullable();
            $table->string('company')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('payment_conditions')->nullable();
            $table->boolean('iva_included')->nullable();
            $table->boolean('has_discount')->default(false);
            $table->string('status')->default('Sin eviar a cliente');
            $table->unsignedDouble('total');
            $table->json('products')->nullable(); //guarda los productos
            $table->json('services')->nullable(); //guarda los servicios
            $table->timestamp('expired_date')->nullable();
            $table->string('notes')->nullable();
            $table->boolean('is_percentage_discount')->nullable(); //el descuento es porcentage
            $table->unsignedFloat('discount')->nullable(); //cantidad fija de descuento.
            $table->unsignedFloat('percentage')->nullable(); //porcentage de descuento.
            $table->string('delivery_type')->nullable();
            $table->unsignedFloat('delivery_cost')->nullable();
            $table->boolean('show_payment_conditions')->default(false);
            $table->boolean('show_address')->default(false);
            $table->boolean('show_expiration')->default(false);
            $table->json('additionals')->nullable();
            $table->foreignId('client_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
