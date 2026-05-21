<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Наполняет /spare-parts-supply-and-procurement-services реальным контентом для секции
 * «Ключевые компетенции и услуги»: две подсекции (groups) — «Палубное применение» и
 * «Машинное отделение». Нормализует структуру JSON `lnkV2.sec2Competencies` под новую
 * схему с `groups[]` (вместо плоских `cards[]`).
 *
 * Откат не делаем — данные пересохраняются админами.
 */
return new class extends Migration
{
    private const SLUG = 'spare-parts-supply-and-procurement-services';

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
                'title' => $this->str($hero['title'] ?? '', 'Судовое снабжение и закупочные услуги'),
                'lead' => $this->str($hero['lead'] ?? '', ''),
                'body' => $this->str($hero['body'] ?? '', '<p></p>'),
            ],
            'sec2Competencies' => [
                'title' => 'Marine Technical Solution поставляет широкий ассортимент судового снабжения',
                'columns' => 3,
                'groups' => [
                    [
                        'title' => 'Палубное применение',
                        'columns' => 3,
                        'cards' => [
                            $this->card('Anchor', 'Грузовое и палубное оборудование'),
                            $this->card('Paintbrush', 'Морские лаки и краски'),
                            $this->card('Cable', 'Верёвки и тросы'),
                            $this->card('ShieldCheck', 'Средства индивидуальной защиты и пожаротушения'),
                        ],
                    ],
                    [
                        'title' => 'Машинное отделение',
                        'columns' => 3,
                        'cards' => [
                            $this->card('CircleDot', 'Подшипники'),
                            $this->card('Zap', 'Электрооборудование и электроинструменты'),
                            $this->card('Wrench', 'Крепления'),
                            $this->card('GitCommitVertical', 'Шланги и муфты'),
                            $this->card('Droplet', 'Смазки и химические реагенты'),
                            $this->card('Ruler', 'Измерительные инструменты'),
                            $this->card('Pipette', 'Трубы и фитинги из стали и композитных материалов GRA / GRE / GRV'),
                            $this->card('Hammer', 'Металлоконструкции'),
                            $this->card('Cog', 'ЗИП для главных и вспомогательных механизмов (MAK, MAN…)'),
                        ],
                    ],
                ],
            ],
            'sec3StrategicAdvantages' => [
                'title' => $this->str($strategic['title'] ?? '', ''),
                'columns' => isset($strategic['columns']) && is_int($strategic['columns']) ? $strategic['columns'] : 3,
                'groups' => $this->preserveGroups($strategic),
            ],
            'sec4TechBase' => [
                'titleHtml' => $this->str($techBase['titleHtml'] ?? '', '<p></p>'),
                'bodyHtml' => $this->str($techBase['bodyHtml'] ?? '', '<p></p>'),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<int, array<string, mixed>>
     */
    private function preserveGroups(array $section): array
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
