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
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('number'); // Número da mesa
            $table->string('name')->nullable(); // Nome customizado
            $table->integer('capacity'); // Capacidade
            $table->enum('status', ['available', 'occupied', 'reserved', 'maintenance'])->default('available');
            $table->string('location')->nullable(); // Área, andar
            $table->string('shape')->nullable(); // round, square, rectangular
            $table->json('position')->nullable(); // x, y para layout visual
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('service_charge', 5, 2)->default(0); // Taxa de serviço
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();

            // Indexes for performance
            $table->index(['company_id', 'status'], 'idx_tables_company_status');
            $table->unique(['company_id', 'number'], 'unq_tables_company_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
