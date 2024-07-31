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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            //informacion personal -----------------------------------------------
            $table->string('name'); //nombre del contacto
            $table->string('company')->nullable(); //nombre de la empresa (en caso de haber)
            $table->string('phone')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('notes')->nullable();
            $table->unsignedDouble('debt')->nullable(); //Deuda acumulada del cliente
            //Domicilio ----------------------------------------------------------
            $table->string('street')->nullable(); //calle
            $table->string('suburb')->nullable(); //colonia
            $table->string('ext_number')->nullable(); // numero de vivienda
            $table->string('int_number')->nullable(); // numero de coto, frac, etc
            $table->string('town')->nullable(); //Municipio
            $table->string('polity_state')->nullable(); //Estado
            $table->string('postal_code')->nullable(); //codigo postal
            //Información fiscal-------------------------------------------------
            $table->string('razon_social')->nullable();
            $table->string('rfc')->nullable();
            $table->string('tax_regime')->nullable();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
