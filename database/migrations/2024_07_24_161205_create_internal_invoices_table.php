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
        Schema::create('internal_invoices', function (Blueprint $table) {
            $table->id();
            // $table->string('folio');
            $table->string('status')->default('Sin factura'); // 1.Sin factura 2.Solicitado, 3.Facturado 
            $table->string('suscription_type');
            $table->unsignedFloat('total_paid');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('end_of_period')->nullable();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_invoices');
    }
};
