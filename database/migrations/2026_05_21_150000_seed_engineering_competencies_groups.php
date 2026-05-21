<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Наполняет /engineering реальным контентом для секции «Ключевые компетенции и услуги»:
 * три подсекции (groups) — «Инжиниринговые услуги», «Используемое программное обеспечение»,
 * «Дисциплины применения». Также включает секцию competencies и нормализует структуру JSON
 * `lnkV2.sec2Competencies` под новую схему с `groups[]` (вместо плоских `cards[]`).
 *
 * Старый плоский `cards[]` для других страниц (lnk / spare-parts) не трогаем — фронтенд
 * мигрирует их «на лету» при чтении (cards → одна группа без заголовка).
 *
 * Откат не делаем — данные пересохраняются админами.
 */
return new class extends Migration
{
    private const SLUG = 'engineering';

    public function up(): void
    {
        if (! Schema::hasTable('content_pages') || ! Schema::hasTable('content_page_translations')) {
            return;
        }

        $pageId = DB::table('content_pages')->where('slug', self::SLUG)->value('id');
        if (! $pageId) {
            return;
        }

        $rows = DB::table('content_page_translations')->where('content_page_id', $pageId)->get();
        foreach ($rows as $row) {
            $body = is_string($row->body ?? null) ? $row->body : '';
            $payload = json_decode($body, true);
            if (! is_array($payload)) {
                continue;
            }

            $payload['lnkV2'] = $this->buildLnkV2(is_array($payload['lnkV2'] ?? null) ? $payload['lnkV2'] : []);
            $payload['lnkPageLayout'] = 'v2';

            $sectionOrder = isset($payload['sectionOrder']) && is_array($payload['sectionOrder'])
                ? $payload['sectionOrder']
                : [];
            if (! in_array('competencies', $sectionOrder, true)) {
                array_unshift($sectionOrder, 'competencies');
            }
            $payload['sectionOrder'] = $sectionOrder;

            $sectionVisibility = isset($payload['sectionVisibility']) && is_array($payload['sectionVisibility'])
                ? $payload['sectionVisibility']
                : [];
            $sectionVisibility['competencies'] = true;
            $payload['sectionVisibility'] = $sectionVisibility;

            DB::table('content_page_translations')->where('id', $row->id)->update([
                'body' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // намеренно без отката — данные пересохраняются админами
    }

    /**
     * Собираем sec2Competencies с тремя подсекциями. Остальные секции lnkV2 (hero, strategicAdvantages,
     * techBase) сохраняем как есть, если они уже были, иначе — пустые.
     *
     * @param  array<string, mixed>  $existing
     * @return array<string, mixed>
     */
    private function buildLnkV2(array $existing): array
    {
        $hero = is_array($existing['sec1Hero'] ?? null) ? $existing['sec1Hero'] : [];
        $techBase = is_array($existing['sec4TechBase'] ?? null) ? $existing['sec4TechBase'] : [];
        $strategic = is_array($existing['sec3StrategicAdvantages'] ?? null) ? $existing['sec3StrategicAdvantages'] : [];

        return [
            'sec1Hero' => [
                'title' => $this->str($hero['title'] ?? '', 'Marine Technical Solution предоставляет следующие инжиниринговые услуги'),
                'lead' => $this->str($hero['lead'] ?? '', ''),
                'body' => $this->str($hero['body'] ?? '', '<p></p>'),
            ],
            'sec2Competencies' => [
                'title' => 'Marine Technical Solution предоставляет следующие инжиниринговые услуги',
                'columns' => 3,
                'groups' => [
                    [
                        'title' => 'Инжиниринговые услуги',
                        'columns' => 3,
                        'cards' => [
                            $this->card('ScanLine', '3D сканирование'),
                            $this->card('Box', '3D моделирование'),
                            $this->card('Wrench', 'Детальный инжиниринг'),
                            $this->card('ClipboardList', 'Подготовка 2D документации'),
                            $this->card('Calculator', 'Расчёты и анализ МКЭ — прочность, расход и падение давления'),
                            $this->card('Eye', 'Надзор за выполнением инженерных работ на аутсорсинге'),
                        ],
                    ],
                    [
                        'title' => 'Используемое программное обеспечение',
                        'columns' => 3,
                        'cards' => [
                            $this->card('Box', 'AutoCAD'),
                            $this->card('Box', 'Navisworks'),
                            $this->card('Box', '3DS Max'),
                            $this->card('Box', 'Plant 3D'),
                            $this->card('Box', 'Inventor Nastran'),
                            $this->card('Box', 'ReCap Pro'),
                        ],
                    ],
                    [
                        'title' => 'Дисциплины применения',
                        'columns' => 3,
                        'cards' => [
                            $this->card('Pipette', 'Системы трубопроводов'),
                            $this->card('Hammer', 'Металлические конструкции и сооружения'),
                            $this->card('Ship', 'Оснащение корпусов'),
                            $this->card('LayoutGrid', 'Фундаменты для оборудования'),
                            $this->card('Zap', 'Электрооборудование и автоматика'),
                        ],
                    ],
                ],
            ],
            'sec3StrategicAdvantages' => [
                'title' => $this->str($strategic['title'] ?? '', ''),
                'columns' => isset($strategic['columns']) && is_int($strategic['columns']) ? $strategic['columns'] : 3,
                'groups' => $this->preserveGroups($strategic, []),
            ],
            'sec4TechBase' => [
                'titleHtml' => $this->str($techBase['titleHtml'] ?? '', '<p></p>'),
                'bodyHtml' => $this->str($techBase['bodyHtml'] ?? '', '<p></p>'),
            ],
        ];
    }

    /**
     * Если в существующем разделе уже есть groups — возвращаем их; иначе оборачиваем cards в одну
     * группу без заголовка; иначе — переданный fallback.
     *
     * @param  array<string, mixed>  $section
     * @param  array<int, array<string, mixed>>  $fallback
     * @return array<int, array<string, mixed>>
     */
    private function preserveGroups(array $section, array $fallback): array
    {
        if (isset($section['groups']) && is_array($section['groups']) && count($section['groups']) > 0) {
            return $section['groups'];
        }
        if (isset($section['cards']) && is_array($section['cards']) && count($section['cards']) > 0) {
            return [[
                'title' => '',
                'cards' => $section['cards'],
            ]];
        }
        if (! empty($fallback)) {
            return $fallback;
        }

        return [['title' => '', 'cards' => []]];
    }

    /**
     * @return array<string, mixed>
     */
    private function card(string $icon, string $title, string $textHtml = '<p></p>'): array
    {
        return [
            'icon' => $icon,
            'hideIcon' => false,
            'title' => $title,
            'text' => $textHtml,
        ];
    }

    private function str(mixed $value, string $default): string
    {
        return is_string($value) && $value !== '' ? $value : $default;
    }
};
