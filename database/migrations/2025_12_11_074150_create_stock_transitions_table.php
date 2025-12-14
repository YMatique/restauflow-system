<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::create('stock_transitions', function (Blueprint $table) {
    //         $table->id();
    //         $table->unsignedBigInteger('produto_id');
    //         $table->integer('quantidade');
    //         $table->enum('tipo', ['entrada', 'saida']);
    //         $table->text('descricao')->nullable();
    //         $table->timestamps();
    //         $table->foreign('produtct_id')->references('id')->on('products')->onDelete('cascade');
    //     });
    // }

    // /**
    //  * Reverse the migrations.
    //  */
    // public function down(): void
    // {
    //     Schema::dropIfExists('stock_transitions');
    // }
};
