<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Приводим `page_inquiries` к набору полей, который требует дизайн формы
 * «Оставьте заявку» (Figma):
 *
 *   – name*           (есть)
 *   – company*        (поле уже было nullable; делаем обязательным на уровне валидации)
 *   – position        (новое, опциональное)
 *   – phone*          (есть)
 *   – email*          (есть)
 *   – vessel_types*   (новое, JSON-массив строк)
 *   – vessels_count*  (новое, целое > 0)
 *   – vessel_flag*    (новое)
 *   – main_ports      (новое, опциональное)
 *   – required_services* (новое, JSON-массив строк)
 *   – comment         (опционально, переименование `message`, теперь nullable)
 *
 * Поля `vessel_name` и `imo` дизайном больше не используются и удаляются.
 *
 * Миграция написана идемпотентно — каждое изменение проверяет наличие колонки,
 * чтобы повторный запуск (или применение на частично обновлённой схеме)
 * не падал.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_inquiries', function (Blueprint $table): void {
            if (! Schema::hasColumn('page_inquiries', 'position')) {
                $table->string('position', 255)->nullable()->after('company');
            }
            if (! Schema::hasColumn('page_inquiries', 'vessel_types')) {
                // JSON-массив выбранных типов судна (machine-readable id, см. enum во фронте).
                $table->json('vessel_types')->nullable()->after('email');
            }
            if (! Schema::hasColumn('page_inquiries', 'vessels_count')) {
                $table->unsignedInteger('vessels_count')->nullable()->after('vessel_types');
            }
            if (! Schema::hasColumn('page_inquiries', 'vessel_flag')) {
                $table->string('vessel_flag', 255)->nullable()->after('vessels_count');
            }
            if (! Schema::hasColumn('page_inquiries', 'main_ports')) {
                $table->string('main_ports', 1000)->nullable()->after('vessel_flag');
            }
            if (! Schema::hasColumn('page_inquiries', 'required_services')) {
                // JSON-массив выбранных услуг (machine-readable id).
                $table->json('required_services')->nullable()->after('main_ports');
            }
        });

        if (Schema::hasColumn('page_inquiries', 'message')) {
            // Раньше message был required + text. Делаем nullable (в дизайне
            // «Особые требования/комментарии» — опциональное поле). Используем
            // raw-statement, т.к. doctrine/dbal в проекте не установлен.
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql' || $driver === 'mariadb') {
                Schema::getConnection()->statement(
                    'ALTER TABLE `page_inquiries` MODIFY `message` TEXT NULL'
                );
            } elseif ($driver === 'pgsql') {
                Schema::getConnection()->statement(
                    'ALTER TABLE "page_inquiries" ALTER COLUMN "message" DROP NOT NULL'
                );
            }
            // Для sqlite (тесты) колонки и так создаются с разрешённым NULL,
            // если новый код не задаёт значение — миграция не требуется.
        }

        Schema::table('page_inquiries', function (Blueprint $table): void {
            if (Schema::hasColumn('page_inquiries', 'vessel_name')) {
                $table->dropColumn('vessel_name');
            }
            if (Schema::hasColumn('page_inquiries', 'imo')) {
                $table->dropColumn('imo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('page_inquiries', function (Blueprint $table): void {
            if (! Schema::hasColumn('page_inquiries', 'vessel_name')) {
                $table->string('vessel_name', 255)->nullable()->after('company');
            }
            if (! Schema::hasColumn('page_inquiries', 'imo')) {
                $table->string('imo', 32)->nullable()->after('vessel_name');
            }
        });

        Schema::table('page_inquiries', function (Blueprint $table): void {
            foreach (['required_services', 'main_ports', 'vessel_flag', 'vessels_count', 'vessel_types', 'position'] as $col) {
                if (Schema::hasColumn('page_inquiries', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
