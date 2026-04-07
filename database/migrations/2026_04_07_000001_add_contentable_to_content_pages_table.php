<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_pages', function (Blueprint $table) {
            $table->string('contentable_type')->nullable();
            $table->unsignedBigInteger('contentable_id')->nullable();
            $table->unique(['contentable_type', 'contentable_id']);
        });
    }

    public function down(): void
    {
        Schema::table('content_pages', function (Blueprint $table) {
            $table->dropUnique(['contentable_type', 'contentable_id']);
            $table->dropColumn(['contentable_type', 'contentable_id']);
        });
    }
};
