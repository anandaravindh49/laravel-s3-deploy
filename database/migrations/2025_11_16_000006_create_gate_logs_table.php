<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gate_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approved_visitor_id')->constrained()->onDelete('cascade');
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->string('visitor_name');
            $table->string('badge_number')->nullable();
            $table->enum('event_type', ['checkin', 'checkout'])->default('checkin');
            $table->timestamp('event_at');
            $table->string('ip_address')->nullable();
            $table->text('metadata')->nullable(); // JSON: device_log_id, biometric_data, etc.
            $table->timestamps();
            
            $table->index('approved_visitor_id');
            $table->index('device_id');
            $table->index('event_type');
            $table->index('event_at');
            $table->index('visitor_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gate_logs');
    }
};
