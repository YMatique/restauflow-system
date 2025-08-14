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
        Schema::table('users', function (Blueprint $table) {
            // User type
            $table->enum('user_type', ['super_admin', 'company_admin', 'company_user'])
                  ->default('company_user')
                  ->after('company_id');
            
            // Status do usuário
            $table->enum('status', ['active', 'inactive', 'suspended'])
                  ->default('active')
                  ->after('user_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
