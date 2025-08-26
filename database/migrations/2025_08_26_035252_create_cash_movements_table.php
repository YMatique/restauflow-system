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
        Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('cascade');
            $table->enum('type', ['in', 'out'])->default('in');
            $table->decimal('amount', 10, 2);
            $table->string('description');
            $table->enum('category', ['sale', 'withdrawal', 'deposit', 'expense', 'tip', 'refund', 'adjustment']);
            $table->dateTime('date');
            $table->text('notes')->nullable();
            
            // Referência a outras tabelas
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            
            // Aprovações
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['company_id', 'shift_id', 'type'], 'idx_cash_movements_company_shift_type');
            $table->index(['reference_type', 'reference_id'], 'idx_cash_movements_reference');
            $table->index(['date', 'category'], 'idx_cash_movements_date_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_movements');
    }
};
