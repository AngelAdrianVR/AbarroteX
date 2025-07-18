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
        Schema::create('service_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('folio')->nullable();
            $table->date('service_date');
            $table->string('client_name')->nullable();
            $table->string('client_department')->nullable();
            $table->string('client_phone_number')->nullable(); // numero telefonico del cliente(nuevo)
            $table->string('cancellation_reason')->nullable(); // razón de cancelacion de orden de servicio (para apontephone)(nuevo)
            $table->string('status')->nullable();
            $table->text('service_description')->nullable(); // descripción del servicio a realizar (nuevo)
            $table->unsignedFloat('service_cost')->nullable(); // Costo del servicio sin tomar en cuenta refacciones (nuevo)
            $table->unsignedFloat('total_cost')->nullable(); // Costo total del servicio mas refacciones (nuevo)
            $table->unsignedFloat('advance_payment')->nullable(); // anticipo (nuevo)
            $table->timestamp('paid_at')->nullable(); // Fecha de pago para saber en que dia asignar la venta (nuevo)
            $table->unsignedFloat('comision_percentage')->nullable(); // porcentage de comision de la persona que lo reparó
            $table->string('payment_method')->nullable(); // metodo de pago (nuevo)
            $table->json('product_details')->nullable(); // todos los detalles del producto como marca, modelo, IMEI, etc
            $table->json('spare_parts')->nullable()->default("[]");
            $table->json('observations')->nullable();
            $table->json('aditionals')->nullable(); // caracteristicas unicas del tipo de servicio, por ejemplo: metodo de desbloqueo.(nuevo)
            $table->string('technician_name')->nullable(); // Responsable del servicio
            $table->string('receiver_name')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_reports');
    }
};
