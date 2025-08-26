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
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('cost', 10, 2)->nullable(); // Custo de produção
            $table->enum('type', ['simple', 'composed'])->default('simple');

            // Para produtos simples (stock direto)
            $table->decimal('stock_quantity', 10, 3)->nullable();
            $table->decimal('min_level', 10, 3)->default(0);
            $table->decimal('cost_per_unit', 10, 2)->nullable();

            // Configurações
            $table->string('image')->nullable();
            $table->json('images')->nullable(); // Multiple images
            $table->string('barcode')->nullable();
            $table->boolean('track_stock')->default(true);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);



            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();

            // Indexes for performance
            $table->index(['company_id', 'category_id', 'is_active'], 'idx_products_company_category_active');
            $table->index(['company_id', 'type', 'is_available'], 'idx_products_company_type_available');
            $table->unique(['company_id', 'slug'], 'unq_products_company_slug');

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
