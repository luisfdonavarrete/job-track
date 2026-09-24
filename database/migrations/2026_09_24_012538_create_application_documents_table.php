<?php

use App\Enums\JobApplications\DocumentType;
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
        Schema::create('application_documents', function (Blueprint $table) {
            $table->id();
            $table->string('original_filename')->nullable(false);
            $table->string('path')->nullable(false);
            $table->string('mime_type')->nullable(false);
            $table->foreignUuid('job_application_id')->constrained('job_applications')->cascadeOnDelete();
            $table->enum('type', DocumentType::cases())->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_documents');
    }
};
