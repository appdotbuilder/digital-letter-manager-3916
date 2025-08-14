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
            $table->enum('role', ['staff', 'manager', 'boss', 'admin'])->default('staff')->after('email_verified_at');
            $table->boolean('can_sign')->default(false)->after('role')->comment('Whether user can digitally sign letters');
            $table->boolean('can_review')->default(false)->after('can_sign')->comment('Whether user can review letters');
            $table->boolean('is_active')->default(true)->after('can_review');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'can_sign', 'can_review', 'is_active']);
        });
    }
};