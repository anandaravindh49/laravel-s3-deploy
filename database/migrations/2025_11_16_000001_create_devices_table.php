<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique(); // Unique device identifier
            $table->string('name'); // Device name (e.g., Gate A)
            $table->string('location'); // Device location
            $table->enum('device_type', ['gate', 'turnstile', 'scanner'])->default('gate');
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->string('ip_address')->nullable();
            $table->string('firmware_version')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->text('metadata')->nullable(); // JSON metadata
            $table->timestamps();
            
            $table->index('device_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
