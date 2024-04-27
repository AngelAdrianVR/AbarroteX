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
        Schema::create('support_reports', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->text('description');
            $table->boolean('first_report')->default(true);
            $table->string('status')->default('Pendiente'); // 1. Pendiente, 2. En proceso, 3. Resuelto.
            $table->string('notes')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('store_id')->nullable()->constrained()->cascadeOnDelete();
            // $table->foreignId('seller_id')->nullable()->constrained('users')->cascadeOnDelete(); //ya tiene la relacion la tienda
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_reports');
    }
};
