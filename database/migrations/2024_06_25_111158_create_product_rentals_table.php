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
        Schema::create('product_rentals', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('period_in_days');
            $table->unsignedFloat('cost');
            $table->string('status')->default('En uso');
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('rented_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->date('estimated_end_date')->nullable();
            $table->time('estimated_end_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_rentals');
    }
};
