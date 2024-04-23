<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Esta migración contiene los usuarios administradores y trabajadores de ezy ventas y seagregó en la app del cliente (tienda)
    // porque la migración store contiene como llave foranea el id de la tabla admins para enlazar las tiendas con su vendedor encargado 
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('phone');
            $table->boolean('is_active')->default(true);
            $table->timestamp('disabled_date')->nullable();
            $table->string('disabled_reason')->nullable();
            $table->json('employee_properties')->nullable();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
