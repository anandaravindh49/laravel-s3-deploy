<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approved_visitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('public_request_id')->constrained()->onDelete('cascade');
            $table->string('visitor_name');
            $table->string('visitor_phone');
            $table->string('id_proof_type')->nullable();
            $table->string('id_proof_number')->nullable();
            $table->text('id_proof_image')->nullable();
            $table->string('purpose_of_visit');
            $table->date('visit_date');
            $table->time('visit_time')->nullable();
            $table->string('host_department');
            $table->string('host_person_name')->nullable();
            $table->string('badge_number')->nullable(); // Badge/pass number
            $table->enum('status', ['pending', 'active', 'checkout', 'expired'])->default('pending');
            $table->text('metadata')->nullable(); // JSON: access_level, device_ids, etc.
            $table->timestamps();
            
            $table->unique('public_request_id');
            $table->index('status');
            $table->index('visit_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approved_visitors');
    }
};
