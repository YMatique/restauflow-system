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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            // Observações
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->timestamps();


            // 🔹 Armazém status
            $table->enum('status', ['active', 'inactive', 'maintenance'])
                ->default('active')
                ->comment('Status do armazém');

            //CONNSTRAIN -- Grantir que cada empresa
            // tenha apenas um stock com mesmo nome
            $table->unique(['company_id', 'name']);

            // 🔹 Indexes extras para performance
            $table->index('company_id', 'idx_stocks_company');
            $table->index('name', 'idx_stocks_name');
            $table->index(['company_id', 'name'], 'idx_stocks_company_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
