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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedFloat('public_price');
            $table->string('currency')->default('$MXN');
            $table->unsignedFloat('cost')->nullable()->default(0);
            $table->string('code')->nullable();
            $table->unsignedFloat('min_stock')->nullable();
            $table->unsignedFloat('max_stock')->nullable();
            $table->unsignedFloat('current_stock')->nullable()->default(1);
            $table->text('description')->nullable();
            $table->boolean('has_inventory_control')->default(true);
            $table->boolean('product_on_request')->default(false); //es producto bajo pedido?
            $table->boolean('bulk_product')->default(false); //es producto a granel?
            $table->string('measure_unit')->nullable(); //en caso de ser a granel
            $table->unsignedSmallInteger('days_for_delivery')->nullable(); //dias habiles de entrega de producto bajo pedido
            $table->json('additional')->nullable();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
