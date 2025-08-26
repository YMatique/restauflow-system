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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Responsável
            $table->dateTime('opened_at');
            $table->decimal('initial_amount', 10, 2)->default(0); // Fundo inicial
            $table->dateTime('closed_at')->nullable();
            $table->decimal('final_amount', 10, 2)->nullable(); // Valor final contado
            $table->decimal('expected_amount', 10, 2)->nullable(); // Valor esperado
            $table->decimal('difference', 10, 2)->nullable(); // Diferença
            $table->decimal('withdrawals', 10, 2)->default(0); // Sangrias
            $table->enum('status', ['open', 'closed', 'auto_closed'])->default('open');
            $table->text('opening_notes')->nullable();
            $table->text('closing_notes')->nullable();
            
            // Performance metrics
            $table->integer('total_orders')->default(0);
            $table->decimal('total_sales', 10, 2)->default(0);
            $table->decimal('cash_sales', 10, 2)->default(0);
            $table->decimal('card_sales', 10, 2)->default(0);
            $table->decimal('digital_sales', 10, 2)->default(0); // M-Pesa, etc
            
            $table->string('terminal_id')->nullable(); // ID do terminal/tablet
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['company_id', 'status'], 'idx_shifts_company_status');
            $table->index(['company_id', 'user_id', 'opened_at'], 'idx_shifts_company_user_opened');
            $table->index(['opened_at', 'closed_at'], 'idx_shifts_dates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
