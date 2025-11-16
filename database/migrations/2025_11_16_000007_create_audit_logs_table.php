<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('module'); // 'device', 'user', 'request', 'approval', 'gate'
            $table->string('action'); // 'create', 'update', 'delete', 'approve', 'reject'
            $table->string('auditable_type')->nullable(); // Model class name
            $table->unsignedBigInteger('auditable_id')->nullable(); // Model ID
            $table->text('old_data')->nullable(); // JSON
            $table->text('new_data')->nullable(); // JSON
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('user_id');
            $table->index('module');
            $table->index('action');
            $table->index(['auditable_type', 'auditable_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
