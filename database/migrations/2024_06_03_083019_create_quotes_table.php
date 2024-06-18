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
            $table->string('contact_name')->nullable();
            $table->unsignedDouble('total');
            $table->json('products')->nullable(); //revisar si es conveniente guardarlos en un json.
            $table->timestamp('expired_date')->nullable();
            $table->string('notes')->nullable();
            $table->boolean('is_percentage_discount')->nullable(); //el descuento es porcentage
            $table->unsignedFloat('discount')->nullable(); //cantidad o porcentage de descuento.
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
