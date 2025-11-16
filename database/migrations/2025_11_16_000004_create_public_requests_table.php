<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_requests', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_name');
            $table->string('visitor_email')->nullable();
            $table->string('visitor_phone');
            $table->string('id_proof_type')->nullable(); // 'passport', 'aadhaar', 'driving_license'
            $table->string('id_proof_number')->nullable();
            $table->text('id_proof_image')->nullable(); // Base64 or file path
            $table->string('purpose_of_visit');
            $table->date('visit_date');
            $table->time('visit_time')->nullable();
            $table->string('host_department');
            $table->string('host_person_name')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('additional_notes')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('visit_date');
            $table->index('approved_by');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_requests');
    }
};
