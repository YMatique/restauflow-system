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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id');
            $table->string('name', 50)->unique();
            $table->text('social_reason');
            $table->string('nuit', 30)->unique();
            $table->string('avatar')->default('company.png');
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->json('settings')->nullable(); // POS configs, currency, etc
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
            
            $table->index('status', 'idx_companies_status');
            $table->index('slug', 'idx_companies_slug');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
