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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_code')->unique(); // Código da reserva
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('table_id')->nullable()->constrained('tables')->onDelete('set null');
            $table->dateTime('reserved_at');
            $table->dateTime('arrived_at')->nullable();
            $table->integer('party_size'); // Número de pessoas
            $table->enum('status', ['confirmed', 'arrived', 'seated', 'completed', 'cancelled', 'no_show'])->default('confirmed');
            $table->text('special_requests')->nullable();
            $table->string('occasion')->nullable(); // Aniversário, etc
            $table->text('notes')->nullable();
            $table->string('phone')->nullable(); // Telefone de contacto
            $table->decimal('deposit', 10, 2)->default(0); // Sinal
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['company_id', 'reserved_at', 'status'], 'idx_reservations_company_date_status');
            $table->unique('reservation_code', 'unq_reservations_code');
            $table->index('client_id', 'idx_reservations_client');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
