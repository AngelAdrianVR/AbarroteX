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
            $table->string('folio')->nullable();
            $table->date('service_date');
            $table->string('client_name')->nullable();
            $table->string('client_department')->nullable();
            $table->json('product_details')->nullable();
            $table->json('spare_parts')->nullable();
            $table->json('observations')->nullable();
            $table->string('technician_name')->nullable();
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
