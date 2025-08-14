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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id');

            $table->foreignId('country_id')
                ->constrained('countries')
                ->onDelete('cascade');

            $table->foreignId('province_id')
                ->constrained('provinces')
                ->onDelete('cascade');

            $table->foreignId('city_id')
                ->constrained('cities')
                ->onDelete('cascade');

            $table->string('street');
            $table->string('postalcode');
            $table->morphs('addressable');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
