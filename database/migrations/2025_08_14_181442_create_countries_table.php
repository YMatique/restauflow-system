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
       Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id');
            $table->string('name')->unique();
            $table->string('code', 3)->unique(); // código ISO, ex: MZ
            $table->string('currency_code', 3)->nullable(); // ex: MZN
            $table->string('currency_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
