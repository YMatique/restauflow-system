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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('document')->nullable(); // CPF, NUIT, etc.
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->json('preferences')->nullable(); // Preferências alimentares
            $table->decimal('credit_limit', 10, 2)->default(0); // Limite de crédito
            $table->decimal('current_balance', 10, 2)->default(0); // Saldo atual
            $table->integer('loyalty_points')->default(0); // Programa fidelidade
            $table->boolean('is_vip')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_visit')->nullable();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();

            // Indexes for performance
            $table->index(['company_id', 'is_active'], 'idx_clients_company_active');
            $table->index(['company_id', 'email'], 'idx_clients_company_email');
            $table->index(['company_id', 'phone'], 'idx_clients_company_phone');
            $table->index(['company_id', 'is_vip'], 'idx_clients_company_vip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
