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
        Schema::create('invectory_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();


            $table->foreignId('inventory_id')->constrained('inventories')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            // Quantidade atual
            $table->decimal('current_stock', 10, 3)->default(0);

            // Controlo por lote/validade
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();

            // Não deixar repetir o mesmo produto/lote no mesmo inventário
            $table->unique(['inventory_id', 'product_id', 'batch_number', 'expiry_date'], 'idx_unique_inventory_item');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invectory_items');
    }
};
