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
        Schema::create('user_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('public_key')->comment('RSA public key for signature verification');
            $table->text('encrypted_private_key')->comment('Encrypted RSA private key');
            $table->string('key_fingerprint')->unique()->comment('SHA256 hash of public key for quick identification');
            $table->timestamp('generated_at')->comment('When the key pair was generated');
            $table->timestamp('expires_at')->nullable()->comment('Optional key expiration');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
            $table->index('key_fingerprint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_keys');
    }
};