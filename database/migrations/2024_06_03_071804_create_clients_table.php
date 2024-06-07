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
            $table->string('name');
            $table->string('last_name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable()->unique();
            $table->unsignedDouble('debt')->nullable(); //Deuda acumulada del cliente
            $table->string('notes')->nullable();
            $table->string('razon_social')->nullable(); //facturación
            $table->string('postal_code')->nullable(); //facturación
            $table->string('rfc')->nullable(); //facturación
            $table->string('tax_regime')->nullable(); //facturación
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
