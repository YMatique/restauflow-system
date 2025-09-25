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
        Schema::create('stock_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained('stocks')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            $table->integer('quantity')->default(0);
            $table->enum('status', ['available', 'reserved', 'damaged'])->default('available');

            $table->foreignId('company_id')->constrained()->onDelete('cascade');



             // Observações
            $table->text('notes')->nullable();

            $table->timestamps();

            // Garantir que cada produto apareça apenas uma vez por stock
            $table->unique(['company_id','stock_id','product_id','status']);


            // Indexes para consultas rápidas
            $table->index('stock_id', 'idx_stock_products_stock');
            $table->index('product_id', 'idx_stock_products_product');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_products');
    }
};
