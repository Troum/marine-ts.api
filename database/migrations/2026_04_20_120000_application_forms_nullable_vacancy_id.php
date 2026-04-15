<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('application_forms', function (Blueprint $table) {
            $table->dropForeign(['vacancy_id']);
        });
        Schema::table('application_forms', function (Blueprint $table) {
            $table->foreignId('vacancy_id')->nullable()->change();
            $table->foreign('vacancy_id')->references('id')->on('vacancies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('application_forms', function (Blueprint $table) {
            $table->dropForeign(['vacancy_id']);
        });
        Schema::table('application_forms', function (Blueprint $table) {
            $table->foreignId('vacancy_id')->nullable(false)->change();
            $table->foreign('vacancy_id')->references('id')->on('vacancies')->cascadeOnDelete();
        });
    }
};
