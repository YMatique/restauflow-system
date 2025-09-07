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
       Schema::create('subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('color', 7)->default('#3B82F6'); // Hex color
            $table->string('emoji')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('image')->nullable();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();

            // Indexes for performance
            $table->index(['company_id', 'category_id', 'is_active', 'sort_order'], 'idx_subcategories_company_category_active_sort');
            $table->unique(['company_id', 'category_id', 'slug'], 'unq_subcategories_company_category_slug');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
