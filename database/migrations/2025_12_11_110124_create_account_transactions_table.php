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
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
              // Conta relacionada
            $table->unsignedBigInteger('account_id');

            // Tipo de transação
            $table->enum('tipo', ['credito', 'debito']);

            // Valor movimentado
            $table->decimal('valor', 15, 2);

            // Saldo após a transação
            $table->decimal('saldo_final', 15, 2)->nullable();

            // Descrição opcional
            $table->text('descricao')->nullable();


            // FK
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_transactions');
    }
};
