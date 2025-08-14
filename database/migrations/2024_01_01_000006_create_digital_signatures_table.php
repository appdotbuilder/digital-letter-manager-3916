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
        Schema::create('digital_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_id')->constrained()->onDelete('cascade');
            $table->foreignId('signer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_key_id')->constrained()->onDelete('cascade');
            $table->text('signature_data')->comment('Encrypted hash of letter content');
            $table->string('content_hash')->comment('SHA256 hash of original content for verification');
            $table->string('algorithm')->default('SHA256withRSA')->comment('Signature algorithm used');
            $table->timestamp('signed_at');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->unique(['letter_id', 'signer_id']);
            $table->index('signer_id');
            $table->index('signed_at');
            $table->index('content_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_signatures');
    }
};