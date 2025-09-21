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
        Schema::create('doc_types', function (Blueprint $table) {
            $table->id();
            $table->string('description', 30);
            $table->string('sigla', 4);
            $table->unsignedBigInteger('numerator')->default(0); // contador/numerador inicial
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('namespace');
            $table->unique(['company_id', 'sigla', 'namespace']); // evita duplicidade de sigla por empresa
            $table->timestamps();

            // Índice para buscas rápidas por company_id e namespace
            $table->index(['company_id', 'namespace']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc_types');
    }
};
