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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_method');
            $table->string('suscription_period');
            $table->string('status')->default('En revisión');
            $table->text('rejected_reason')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedFloat('amount');
            $table->unsignedFloat('days_gifted')->default(0);
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('validated_by_id')->nullable()->constrained('admins')->cascadeOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
