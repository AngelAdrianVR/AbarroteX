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
        Schema::create('online_sales', function (Blueprint $table) {
            $table->id();
            //información personal -----------------
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            //Domicilio ----------------------------
            $table->string('suburb'); //colonia
            $table->string('street'); //calle
            $table->string('ext_number');
            $table->string('int_number')->nullable();
            $table->string('town')->nullable(); //Municipio
            $table->string('polity_state')->nullable(); //Estado
            $table->string('postal_code')->nullable(); //codigo postal
            $table->string('address_references')->nullable(); //referencias de la vivienda
            //Información extra -------------------
            $table->string('payment_method')->default('Efectivo');
            $table->string('status')->default('Pendiente'); // estatus del pedido 
            $table->json('products')->nullable(); // se guarda el arreglo del carrito recuperado del localStorage
            $table->unsignedFloat('delivery_price'); // costo de envío
            $table->unsignedFloat('total'); // total de venta $
            $table->timestamp('delivered_at')->nullable();
            $table->foreignId('store_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_sales');
    }
};
