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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // tipo de tienda ej. ropa, carniceria, abarrotes
            $table->string('contact_name');
            $table->json('online_store_properties')->nullable(); //whatsapp,delivery_price,delivery_conditions,min_free_delivery, metodos de pago
            $table->json('printer_config')->nullable(); //información de configuración de impresora
            $table->json('activated_modules')->nullable(); //modulos activados para la suscripción
            $table->string('contact_phone')->nullable(); // telefono de contacto
            $table->string('address')->nullable();
            $table->string('plan')->default('Plan Básico');
            $table->string('suscription_period')->default('Periodo de prueba');
            $table->unsignedBigInteger('default_card_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('next_payment')->nullable()->default(now()->addDays(15)->toDateString());
            // se agregó en este proyecto la migracion de admins 
            // porque al migrar las tablas tiene que ir en primer lugar
            $table->foreignId('seller_id')->nullable()->constrained('admins')->cascadeOnDelete(); 
            $table->string('status')->default('Pagado');
            $table->json('colors')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
