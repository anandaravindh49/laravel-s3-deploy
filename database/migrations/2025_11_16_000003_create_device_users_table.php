<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->string('external_user_id'); // User ID from device
            $table->string('name');
            $table->string('id_proof')->nullable(); // ID proof (card, badge, etc.)
            $table->enum('access_level', ['visitor', 'employee', 'contractor'])->default('visitor');
            $table->enum('status', ['active', 'revoked', 'expired'])->default('active');
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            
            $table->unique(['device_id', 'external_user_id']);
            $table->index('device_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_users');
    }
};
