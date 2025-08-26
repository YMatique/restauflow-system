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
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
        
            // Snapshot dos dados no momento da venda
            $table->string('product_name');
            
            // Quantidades e preços
            $table->decimal('quantity', 8, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();
            // Indexes for performance
            $table->index(['company_id', 'sale_id'], 'idx_sale_items_company_sale');
            $table->index(['company_id', 'product_id'], 'idx_sale_items_company_product');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
