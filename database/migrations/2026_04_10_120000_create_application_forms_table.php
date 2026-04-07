<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacancy_id')->constrained('vacancies')->cascadeOnDelete();
            $table->string('status', 32)->index();
            $table->string('full_name', 500);
            $table->string('email', 255)->index();
            $table->string('phone', 128)->nullable();
            $table->json('payload');
            $table->string('document_upload_token_hash', 64)->nullable()->unique();
            $table->timestamp('document_upload_token_expires_at')->nullable();
            $table->json('requested_document_keys')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_forms');
    }
};
