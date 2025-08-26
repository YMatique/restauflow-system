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
            // Add new fields for restaurant system
            $table->enum('role', ['owner', 'manager', 'cashier', 'stock_manager', 'waiter'])->default('cashier')->after('password');
            $table->boolean('is_active')->default(true)->after('role');
            $table->string('phone')->nullable()->after('is_active');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            
            // Indexes for performance
            $table->index(['company_id', 'is_active'], 'idx_users_company_active');
            $table->index(['company_id', 'role'], 'idx_users_company_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropIndex('idx_users_company_active');
            $table->dropIndex('idx_users_company_role');
            $table->dropColumn(['role', 'is_active', 'phone', 'company_id']);
        });
    }
};
