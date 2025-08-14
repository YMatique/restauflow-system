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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('max_users')->nullable(); // null = unlimited
            $table->integer('max_orders')->nullable(); // null = unlimited
            $table->json('features')->nullable(); // funcionalidades do plano
            $table->decimal('price_mzn', 12, 2)->default(0);
            $table->decimal('price_usd', 12, 2)->default(0);
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'annual'])->default('monthly');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index(['is_active']);
            $table->index(['sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
