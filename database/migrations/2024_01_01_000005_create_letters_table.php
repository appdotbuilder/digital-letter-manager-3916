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
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content')->comment('Rich text content of the letter');
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'signed', 'rejected'])
                  ->default('draft');
            $table->string('recipient_name')->nullable();
            $table->text('recipient_address')->nullable();
            $table->string('reference_number')->unique()->nullable()->comment('Auto-generated reference');
            $table->foreignId('template_id')->nullable()->constrained('letter_templates')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_reviewer')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_signer')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index(['created_by', 'status']);
            $table->index('assigned_reviewer');
            $table->index('assigned_signer');
            $table->index('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};