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
        Schema::create('credit_sale_data', function (Blueprint $table) {
            $table->id();
            $table->string('folio');
            $table->date('expired_date')->nullable();
            $table->string('status'); //Pendiente cuando no hay abono, Parcial cuando tiene, Pagado
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_sale_data');
    }
};
