<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 64)->nullable();
            $table->string('company', 255)->nullable();
            $table->string('vessel_name', 255)->nullable();
            $table->string('imo', 32)->nullable();
            $table->text('message');
            $table->string('source_page', 255);
            $table->string('ip', 45)->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_inquiries');
    }
};
