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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_name');
            $table->string('contact_phone')->nullable();
            $table->string('address')->nullable();
            $table->string('plan')->default('Plan Básico');
            $table->string('suscription_period');
            $table->unsignedBigInteger('default_card_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('next_payment')->nullable()->default(now()->addDays(15)->toDateString());
            $table->foreignId('seller_id')->nullable()->constrained('admins')->cascadeOnDelete(); // se agregó en este proyecto la migracion de admins 
                                                                                        // porque al migrar las tablas tiene que ir en primer lugar
            $table->string('status')->default('Pagado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
