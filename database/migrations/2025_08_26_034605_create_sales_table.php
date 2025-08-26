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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // Número da fatura
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('table_id')->nullable()->constrained('tables')->onDelete('set null');
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // Vendedor
            
            // Datas
            $table->dateTime('sold_at');
            $table->timestamp('completed_at')->nullable();
            
            // Valores
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('service_charge', 10, 2)->default(0); // Taxa de serviço
            $table->decimal('total', 10, 2);
            
            // Pagamento
            $table->enum('payment_method', ['cash', 'card', 'mbway', 'mpesa', 'transfer', 'credit', 'mixed'])->default('cash');
            $table->json('payment_details')->nullable(); // Detalhes do pagamento misto
            $table->enum('status', ['pending', 'completed', 'cancelled', 'refunded'])->default('pending');
            
            // Informações adicionais
            $table->text('notes')->nullable();
            $table->enum('sale_type', ['dine_in', 'takeaway', 'delivery'])->default('dine_in');
            $table->integer('customer_count')->default(1); // Número de pessoas
            $table->json('split_details')->nullable(); // Detalhes da divisão da conta
            
            
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['company_id', 'status', 'sold_at'], 'idx_sales_company_status_date');
            $table->index(['company_id', 'shift_id'], 'idx_sales_company_shift');
            $table->index(['company_id', 'user_id'], 'idx_sales_company_user');
            $table->unique('invoice_number', 'unq_sales_invoice_number');
            $table->index('sold_at', 'idx_sales_sold_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
