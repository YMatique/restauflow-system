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
            $table->text('desc')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
            $table->index(['status']);
            $table->index(['created_at']);
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
