<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('path', 500);
            $table->string('alt', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $rows = [];
        $paths = [
            '/images/gallery/gallery-01.jpeg',
            '/images/gallery/gallery-02.png',
            '/images/gallery/gallery-03.png',
            '/images/gallery/gallery-04.png',
            '/images/gallery/gallery-05.png',
            '/images/gallery/gallery-06.png',
            '/images/gallery/gallery-07.png',
            '/images/gallery/gallery-08.png',
            '/images/gallery/gallery-09.png',
            '/images/gallery/gallery-10.png',
            '/images/gallery/gallery-11.png',
            '/images/gallery/gallery-12.png',
            '/images/gallery/gallery-13.png',
        ];
        $alts = [
            'Судоремонт: работы на объекте',
            'Техническое обслуживание судна',
            'Инженерно-технический персонал',
            'Ремонтное оборудование',
            'Доковые работы',
            'Судовые механизмы',
            'Производственный процесс',
            'Работы в порту',
            'Объект ремонта',
            'Судоремонтные работы',
            'Техника и инструмент',
            'Выполнение работ',
            'Marine Technical Solutions: объект',
        ];
        foreach ($paths as $i => $path) {
            $rows[] = [
                'path' => $path,
                'alt' => $alts[$i] ?? null,
                'sort_order' => $i + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('gallery_items')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_items');
    }
};
