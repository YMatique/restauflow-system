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
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade'); // cliente
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('cascade');   // turno/shift
            $table->string('invoice_number')->unique();
            $table->dateTime('sold_at');
            $table->decimal('total', 10, 2);
            $table->enum('payment_method', ['cash', 'card', 'mpesa', 'other'])->default('cash');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
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
