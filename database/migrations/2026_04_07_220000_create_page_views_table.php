<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        Schema::create('page_views', function (Blueprint $table) use ($driver) {
            $table->id();
            $table->string('path', 2048);
            $table->string('title', 512)->nullable();
            $table->string('referrer', 2048)->nullable();
            $table->char('ip_hash', 64);
            $table->timestamp('created_at')->useCurrent();

            $table->index('created_at');

            if ($driver !== 'mysql') {
                $table->index(['path', 'created_at']);
            }
        });

        if ($driver === 'mysql') {
            // Prefix on path: full VARCHAR(2048) exceeds InnoDB max key length (3072 bytes with utf8mb4).
            DB::statement('ALTER TABLE `page_views` ADD INDEX `page_views_path_created_at_index` (`path`(191), `created_at`)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
