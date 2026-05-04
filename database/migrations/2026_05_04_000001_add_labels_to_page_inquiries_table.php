<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_inquiries', function (Blueprint $table) {
            $table->json('vessel_type_labels')->nullable()->after('vessel_types');
            $table->json('required_service_labels')->nullable()->after('required_services');
        });
    }

    public function down(): void
    {
        Schema::table('page_inquiries', function (Blueprint $table) {
            $table->dropColumn(['vessel_type_labels', 'required_service_labels']);
        });
    }
};
