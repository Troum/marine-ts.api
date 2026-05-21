<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Переводим страницы /engineering и /spare-parts-supply-and-procurement-services
 * из формата content_pages «customSections + articleHtml» в LineMarketing JSON
 * (структура lnkV2: hero + sec2Competencies + sec3StrategicAdvantages + sec4TechBase).
 *
 * Источник:
 *   - hero берётся из первого блока customSections[0].blocks[0] (heroImage):
 *       imageUrl → heroBackgroundImage,
 *       title    → sec1Hero.title (plain text),
 *       caption  → sec1Hero.lead  (plain text).
 *   - Карточки строятся из ячеек mts-rich-grid, содержащих списки `<ul><li>...`:
 *       заголовок ячейки (`<p><strong>` или предшествующий `<h3>/<h4>`) — заголовок карточки,
 *       пункты `<li>` — текст карточки (по одному <li> в карточке).
 *     Если в ячейке только список без заголовка — карточка без подписи.
 *   - Прозаический текст и картинки уходят в sec4TechBase.bodyHtml как rich-HTML
 *     (чтобы ничего не потерялось при миграции).
 *
 * Откат не делаем — данные пересохраняются админами.
 */
return new class extends Migration
{
    private const SLUGS = [
        'engineering',
        'spare-parts-supply-and-procurement-services',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('content_pages') || ! Schema::hasTable('content_page_translations')) {
            return;
        }

        foreach (self::SLUGS as $slug) {
            $pageId = DB::table('content_pages')->where('slug', $slug)->value('id');
            if (! $pageId) {
                continue;
            }
            $rows = DB::table('content_page_translations')
                ->where('content_page_id', $pageId)
                ->get();
            foreach ($rows as $row) {
                $body = is_string($row->body ?? null) ? $row->body : '';
                $converted = $this->convertBody($body);
                if ($converted === null) {
                    continue;
                }
                DB::table('content_page_translations')
                    ->where('id', $row->id)
                    ->update([
                        'body' => $converted,
                        'updated_at' => now(),
                    ]);
            }
        }

        $this->ensureSeoPages();
    }

    public function down(): void
    {
        // намеренно без отката
    }

    private function convertBody(string $body): ?string
    {
        if ($body === '') {
            return null;
        }
        $data = json_decode($body, true);
        if (! is_array($data)) {
            return null;
        }
        // Если уже похоже на LineMarketing-структуру — пропускаем.
        if (isset($data['lnkV2']) || isset($data['lnkPageLayout']) || isset($data['hero']['titleFormatted'])) {
            return null;
        }

        $hero = $this->extractHero($data);
        [$cards, $leftoverHtml] = $this->extractCardsAndLeftover($data);

        $lnkV2 = [
            'sec1Hero' => [
                'title' => $hero['title'],
                'lead' => $hero['lead'],
                'body' => '<p></p>',
            ],
            'sec2Competencies' => [
                'title' => '',
                'columns' => 2,
                'cards' => $cards,
            ],
            'sec3StrategicAdvantages' => [
                'title' => '',
                'columns' => 3,
                'cards' => [],
            ],
            'sec4TechBase' => [
                'titleHtml' => '<p></p>',
                'bodyHtml' => $leftoverHtml !== '' ? $leftoverHtml : '<p></p>',
            ],
        ];

        $payload = [
            'hero' => [
                'label' => '',
                'titleFormatted' => [
                    'spans' => [
                        ['text' => $hero['title'], 'tone' => 'text'],
                    ],
                ],
                'lead' => $hero['lead'],
            ],
            'heroButtons' => [],
            'lnkPageLayout' => 'v2',
            'lnkV2' => $lnkV2,
            'sectionOrder' => ['competencies', 'strategicAdvantages', 'techBase'],
            'sectionVisibility' => [
                'competencies' => count($cards) > 0,
                'strategicAdvantages' => false,
                'techBase' => $leftoverHtml !== '',
            ],
            'customSections' => [],
            'directionsSection' => ['title' => '', 'lead' => ''],
            'directions' => [],
            'principles' => ['title' => '', 'items' => []],
            'audience' => [
                'title' => '',
                'paragraph1' => '',
                'paragraph2' => '',
                'ctaLabel' => '',
                'ctaHref' => '/contacts',
            ],
            'checklist' => ['sectionTitle' => '', 'intro' => '', 'sections' => []],
            'showInquiryForm' => true,
            'hideInquiryFormIntro' => false,
            'hideInquiryFormCardHeading' => false,
            'inquiryForm' => [
                'vesselTypes' => [],
                'requiredServices' => [],
                'vesselTypeLabels' => [],
                'requiredServiceLabels' => [],
            ],
            'heroBackgroundImage' => $hero['image'],
            'sectionBackgroundImages' => [],
        ];

        return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{title: string, lead: string, image: string}
     */
    private function extractHero(array $data): array
    {
        $title = '';
        $lead = '';
        $image = '';
        $customSections = $data['customSections'] ?? null;
        if (is_array($customSections)) {
            foreach ($customSections as $section) {
                if (! is_array($section)) {
                    continue;
                }
                $blocks = $section['blocks'] ?? null;
                if (! is_array($blocks)) {
                    continue;
                }
                foreach ($blocks as $block) {
                    if (! is_array($block) || ($block['type'] ?? null) !== 'heroImage') {
                        continue;
                    }
                    if ($title === '') {
                        $title = $this->htmlToPlain((string) ($block['title'] ?? ''));
                    }
                    if ($lead === '') {
                        $lead = $this->htmlToPlain((string) ($block['caption'] ?? ''));
                    }
                    if ($image === '' && is_string($block['imageUrl'] ?? null)) {
                        $image = (string) $block['imageUrl'];
                    }
                }
            }
        }

        return ['title' => $title, 'lead' => $lead, 'image' => $image];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{0: array<int, array<string, mixed>>, 1: string}
     */
    private function extractCardsAndLeftover(array $data): array
    {
        $articleHtml = is_string($data['articleHtml'] ?? null) ? $data['articleHtml'] : '';
        if (trim($articleHtml) === '') {
            return [[], ''];
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $loaded = $dom->loadHTML(
            '<?xml encoding="UTF-8"?><div id="root">'.$articleHtml.'</div>',
            LIBXML_NOWARNING | LIBXML_NOERROR
        );
        libxml_clear_errors();
        if (! $loaded) {
            return [[], $articleHtml];
        }
        $xpath = new DOMXPath($dom);
        $cells = $xpath->query("//*[contains(@class, 'mts-rich-grid-cell')]");
        if ($cells === false || $cells->length === 0) {
            return [[], $articleHtml];
        }

        $cards = [];
        $leftoverHtmlParts = [];
        $icons = ['ClipboardList', 'Cog', 'Wrench', 'Ship', 'Anchor', 'Truck', 'Package', 'Settings'];
        $iconIndex = 0;

        foreach ($cells as $cell) {
            if (! $cell instanceof DOMElement) {
                continue;
            }
            $listItems = $this->collectListItems($cell);
            $cellHeading = $this->extractCellHeadingPlain($cell);
            $strippedCardHtml = $this->extractCellNonListHtml($cell);

            if (! empty($listItems)) {
                // Каждый <li> внутри ячейки становится отдельной карточкой.
                $groupTitle = $cellHeading;
                foreach ($listItems as $itemText) {
                    $cards[] = [
                        'icon' => $icons[$iconIndex % count($icons)],
                        'hideIcon' => false,
                        'title' => $itemText,
                        'text' => $groupTitle !== ''
                            ? '<p>'.$this->escapeHtml($groupTitle).'</p>'
                            : '<p></p>',
                    ];
                    $iconIndex++;
                }
                if (trim($strippedCardHtml) !== '') {
                    $leftoverHtmlParts[] = $strippedCardHtml;
                }
                continue;
            }

            // Ячейка без списков — пускаем целиком в техбазу.
            $cellHtml = $this->innerHtml($cell);
            if (trim($cellHtml) !== '') {
                $leftoverHtmlParts[] = $cellHtml;
            }
        }

        $leftover = $this->sanitizeLeftoverHtml(trim(implode("\n", $leftoverHtmlParts)));

        return [$cards, $leftover];
    }

    /**
     * @return array<int, string>
     */
    private function collectListItems(DOMElement $cell): array
    {
        $items = [];
        $lis = $cell->getElementsByTagName('li');
        foreach ($lis as $li) {
            $text = trim($this->nodeText($li));
            if ($text !== '') {
                $items[] = $text;
            }
        }

        return $items;
    }

    private function extractCellHeadingPlain(DOMElement $cell): string
    {
        // Сильный кандидат — `<p><strong>...</strong></p>` без вложенного <ul>.
        foreach ($cell->getElementsByTagName('strong') as $strong) {
            $parent = $strong->parentNode;
            if ($parent instanceof DOMElement && in_array($parent->nodeName, ['p', 'span'], true)) {
                $text = trim($this->nodeText($strong));
                if ($text !== '' && ! str_ends_with($text, ':')) {
                    return $text;
                }
                if ($text !== '') {
                    return rtrim($text, ':');
                }
            }
        }
        foreach (['h2', 'h3', 'h4'] as $tag) {
            $els = $cell->getElementsByTagName($tag);
            if ($els->length > 0) {
                $text = trim($this->nodeText($els->item(0)));
                if ($text !== '') {
                    return $text;
                }
            }
        }

        return '';
    }

    /**
     * Возвращает HTML ячейки без списков (если в ней были и текст, и списки —
     * текстовый «остаток» переезжает в техбазу, чтобы не потерялся).
     */
    private function extractCellNonListHtml(DOMElement $cell): string
    {
        $clone = $cell->cloneNode(true);
        if (! $clone instanceof DOMElement) {
            return '';
        }
        $toRemove = [];
        foreach (['ul', 'ol'] as $tag) {
            $els = $clone->getElementsByTagName($tag);
            foreach ($els as $el) {
                $toRemove[] = $el;
            }
        }
        foreach ($toRemove as $el) {
            if ($el->parentNode instanceof DOMNode) {
                $el->parentNode->removeChild($el);
            }
        }
        $html = $this->innerHtml($clone);

        return trim($html);
    }

    private function innerHtml(DOMElement $node): string
    {
        $out = '';
        foreach ($node->childNodes as $child) {
            $out .= $node->ownerDocument->saveHTML($child);
        }

        return $out;
    }

    private function nodeText(DOMNode $node): string
    {
        $text = $node->textContent ?? '';
        // Удаляем мусорные пробелы/неразрывники, оставляем нормальный текст.
        $text = preg_replace('/\s+/u', ' ', $text);

        return $text === null ? '' : trim($text);
    }

    private function htmlToPlain(string $html): string
    {
        $stripped = strip_tags($html);
        $stripped = html_entity_decode($stripped, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $stripped = preg_replace('/\s+/u', ' ', $stripped) ?? '';

        return trim($stripped);
    }

    private function escapeHtml(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function sanitizeLeftoverHtml(string $html): string
    {
        if ($html === '') {
            return '';
        }
        // Убираем пустые `<p></p>` подряд (артефакты редактора).
        $html = preg_replace('#(?:<p>\s*</p>\s*){2,}#u', '<p></p>', $html) ?? $html;

        return $html;
    }

    private function ensureSeoPages(): void
    {
        if (! Schema::hasTable('site_seo_pages') || ! Schema::hasTable('site_seo_page_translations')) {
            return;
        }
        $now = now();
        $locale = (string) config('marine.default_locale', 'ru');

        $pages = [
            'engineering' => [
                'label' => 'Инжиниринговые услуги',
                'seo_title' => 'Инжиниринговые услуги — Marine Technical Solutions',
                'seo_description' => '3D-сканирование, моделирование, расчёты МКЭ, надзор и подготовка технической документации для морского флота.',
                'seo_keywords' => 'инжиниринг, 3D сканирование, 3D моделирование, МКЭ, морской инжиниринг',
            ],
            'spare-parts-supply-and-procurement-services' => [
                'label' => 'Судовое снабжение и услуги по закупкам',
                'seo_title' => 'Судовое снабжение и закупки — Marine Technical Solutions',
                'seo_description' => 'Поставки запасных частей, оборудования и материалов для палубных и машинных отделений морских судов.',
                'seo_keywords' => 'судовое снабжение, ЗИП, закупки, судовые запчасти, оборудование для флота',
            ],
        ];

        foreach ($pages as $slug => $row) {
            $pageId = DB::table('site_seo_pages')->where('slug', $slug)->value('id');
            if ($pageId === null) {
                $pageId = DB::table('site_seo_pages')->insertGetId([
                    'slug' => $slug,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
            $exists = DB::table('site_seo_page_translations')
                ->where('site_seo_page_id', $pageId)
                ->where('locale', $locale)
                ->exists();
            if ($exists) {
                continue;
            }
            $translation = [
                'site_seo_page_id' => $pageId,
                'locale' => $locale,
                'label' => $row['label'],
                'seo_title' => $row['seo_title'],
                'seo_description' => $row['seo_description'],
                'seo_keywords' => $row['seo_keywords'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
            if (Schema::hasColumn('site_seo_page_translations', 'seo_image')) {
                $translation['seo_image'] = null;
            }
            DB::table('site_seo_page_translations')->insert($translation);
        }
    }
};
