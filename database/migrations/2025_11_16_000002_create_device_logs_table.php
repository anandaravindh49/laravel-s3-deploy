<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->string('event_type'); // 'sync_request', 'user_list_push', 'error'
            $table->text('log_data')->nullable(); // JSON data
            $table->enum('status', ['success', 'failed', 'pending'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('logged_at')->useCurrent();
            $table->timestamps();
            
            $table->index('device_id');
            $table->index('event_type');
            $table->index('status');
            $table->index('logged_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_logs');
    }
};
