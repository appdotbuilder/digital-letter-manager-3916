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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type')->comment('Type of action performed');
            $table->text('description')->comment('Human readable description of the event');
            $table->json('event_data')->nullable()->comment('Additional event context and data');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->morphs('auditable', 'audit_logs_auditable_index');
            $table->timestamp('performed_at');
            $table->timestamps();
            
            $table->index(['event_type', 'performed_at']);
            $table->index(['user_id', 'performed_at']);
            $table->index('performed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};