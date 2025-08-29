<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('event');  // e.g., 'created', 'updated', 'deleted', 'login', 'logout'
            $table->string('auditable_type');  // The model/table being affected
            $table->unsignedBigInteger('auditable_id')->nullable();  // ID of the affected record
            $table->string('url')->nullable();  // The URL where the action occurred
            $table->ipAddress('ip_address')->nullable();  // User's IP address
            
            // User Information
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('user_type')->nullable();  // In case users have different types (admin, teacher, etc.)
            $table->text('user_agent')->nullable();  // Browser/device info
            
            // Change Tracking
            $table->json('old_values')->nullable();  // Data before change (for updates/deletes)
            $table->json('new_values')->nullable();  // Data after change (for creates/updates)
            
            // Additional Context
            $table->string('tags')->nullable();  // For categorization (e.g., 'security', 'grades', 'attendance')
            $table->text('description')->nullable();  // Human-readable description of the event
            
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index(['auditable_type', 'auditable_id']);
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
};
