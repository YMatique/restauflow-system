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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
              // Referência ao produto (simplificado, só produtos por enquanto)
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            
            // Tipo de movimento
            $table->enum('type', ['in', 'out', 'adjustment', 'sale', 'return', 'loss', 'expired']);
            
            // Quantidades
            $table->decimal('quantity', 10, 3); // Quantidade movimentada (+ ou -)
            $table->decimal('previous_stock', 10, 3);
            $table->decimal('new_stock', 10, 3);
            $table->decimal('unit_cost', 10, 4)->nullable(); // Custo unitário
            
            // Referências
            $table->string('reference_type')->nullable(); // Sale, Purchase, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            
            // Detalhes
            $table->string('reason');
            $table->text('notes')->nullable();
            $table->dateTime('date');
            $table->string('batch_number')->nullable(); // Lote
            $table->date('expiry_date')->nullable();
            
            // Fornecedor/destino
            $table->string('supplier')->nullable();
            $table->string('invoice_number')->nullable();
            
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['company_id', 'product_id'], 'idx_stock_movements_company_product');
            $table->index(['company_id', 'type', 'date'], 'idx_stock_movements_company_type_date');
            $table->index(['reference_type', 'reference_id'], 'idx_stock_movements_reference');
            $table->index(['date', 'type'], 'idx_stock_movements_date_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
