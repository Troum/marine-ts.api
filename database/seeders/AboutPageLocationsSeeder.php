<?php

namespace Database\Seeders;

use App\Models\ContentPage;
use Illuminate\Database\Seeder;

/**
 * Заполняет блок «География технического обслуживания» на странице
 * "О компании" (`content_pages.slug = about`) точными координатами портов.
 *
 * Поведение:
 *   • Если страница `about` существует и в её body-переводе уже валидный
 *     JSON c полем `hero` (т.е. админ хотя бы раз сохранял страницу) —
 *     обновляются ТОЛЬКО `geography.locations`, остальные поля
 *     (тексты, миссия, сертификаты, фоновые изображения) НЕ трогаются.
 *   • Если страница не найдена или body пустой/невалидный — сидер
 *     печатает предупреждение и завершает работу. В этом случае нужно
 *     один раз сохранить страницу в админке (она подтянет дефолты из
 *     `aboutPageDefaults.ts`), затем перезапустить сидер.
 *
 * Идея «не перезаписывать всё подряд»: пользователь часто редактирует
 * тексты и принципы вручную. Сидер для координат должен быть точечным,
 * иначе один `php artisan db:seed` затрёт правки админа.
 *
 * Координаты — портовые, не центры городов. Точность 4 знака после
 * запятой = ~11 м на экваторе, чего более чем достаточно для маркеров
 * на глобальной карте Mapbox с zoom 1–3. Те же значения зашиты в
 * `app/app/utils/aboutPageDefaults.ts` (frontend defaults) — менять
 * нужно в обоих местах.
 *
 * Запуск отдельно:
 *   php artisan db:seed --class=Database\\Seeders\\AboutPageLocationsSeeder
 */
class AboutPageLocationsSeeder extends Seeder
{
    /**
     * Структурные поля (lng/lat/labelOnRight) общие для всех локалей,
     * локализуется только `name`.
     *
     * @var list<array{
     *     lng: float,
     *     lat: float,
     *     labelOnRight: bool,
     *     names: array<string, string>
     * }>
     */
    private const LOCATIONS = [
        // Калининград — Калининградский морской торговый порт.
        [
            'lng' => 20.4945,
            'lat' => 54.7066,
            'labelOnRight' => false,
            'names' => ['ru' => 'Калининград', 'en' => 'Kaliningrad'],
        ],
        // Клайпеда — Klaipėdos jūrų uostas (главный портовый канал).
        [
            'lng' => 21.1192,
            'lat' => 55.7007,
            'labelOnRight' => true,
            'names' => ['ru' => 'Клайпеда', 'en' => 'Klaipėda'],
        ],
        // Халл — Port of Hull (King George Dock на восточной стороне устья Хамбера).
        [
            'lng' => -0.2589,
            'lat' => 53.7290,
            'labelOnRight' => true,
            'names' => ['ru' => 'Халл', 'en' => 'Hull'],
        ],
        // Стокгольм — Frihamnen (главный коммерческий порт).
        [
            'lng' => 18.1117,
            'lat' => 59.3496,
            'labelOnRight' => false,
            'names' => ['ru' => 'Стокгольм', 'en' => 'Stockholm'],
        ],
        // Олесунн — Skansekaia, основной грузовой терминал.
        [
            'lng' => 6.1496,
            'lat' => 62.4724,
            'labelOnRight' => false,
            'names' => ['ru' => 'Олесунн', 'en' => 'Ålesund'],
        ],
        // Лас-Пальмас — Puerto de La Luz (один из крупнейших портов Атлантики).
        [
            'lng' => -15.4148,
            'lat' => 28.1410,
            'labelOnRight' => true,
            'names' => ['ru' => 'Лас-Пальмас', 'en' => 'Las Palmas'],
        ],
        // Александрия — Western Harbour, главный коммерческий порт Египта.
        [
            'lng' => 29.8669,
            'lat' => 31.1925,
            'labelOnRight' => false,
            'names' => ['ru' => 'Александрия', 'en' => 'Alexandria'],
        ],
        // Дубай — порт Джебель-Али (крупнейший контейнерный порт Ближнего Востока).
        [
            'lng' => 55.1107,
            'lat' => 25.0213,
            'labelOnRight' => false,
            'names' => ['ru' => 'Дубай (Джебель-Али)', 'en' => 'Dubai (Jebel Ali)'],
        ],
        // Панама — Бальбоа (тихоокеанский вход в Панамский канал).
        [
            'lng' => -79.5630,
            'lat' => 8.9528,
            'labelOnRight' => true,
            'names' => ['ru' => 'Панама (Бальбоа)', 'en' => 'Panama (Balboa)'],
        ],
        // Кюрасао — Виллемстад, бухта Schottegat.
        [
            'lng' => -68.9333,
            'lat' => 12.1138,
            'labelOnRight' => true,
            'names' => ['ru' => 'Кюрасао (Виллемстад)', 'en' => 'Curaçao (Willemstad)'],
        ],
    ];

    public function run(): void
    {
        /** @var ContentPage|null $page */
        $page = ContentPage::query()->where('slug', 'about')->first();

        if (! $page) {
            $this->command?->warn(
                'AboutPageLocationsSeeder: страница "about" в content_pages не найдена. '
                . 'Откройте админку → «О компании» → «Сохранить» один раз, чтобы создать запись, '
                . 'затем перезапустите сидер.'
            );

            return;
        }

        $updated = 0;
        $skipped = 0;

        foreach ($page->translations as $translation) {
            $locale = (string) $translation->locale;
            $rawBody = (string) $translation->body;

            $parsed = json_decode($rawBody, true);
            if (! is_array($parsed) || ! isset($parsed['hero'])) {
                $this->command?->warn(sprintf(
                    'AboutPageLocationsSeeder: локаль "%s" — body пустой или не содержит структуру AboutPageData; пропускаем.',
                    $locale
                ));
                $skipped++;

                continue;
            }

            $geography = is_array($parsed['geography'] ?? null) ? $parsed['geography'] : [];
            $geography['locations'] = $this->buildLocationsFor($locale);
            $parsed['geography'] = $geography;

            $translation->body = json_encode(
                $parsed,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
            $translation->save();
            $updated++;
        }

        $this->command?->info(sprintf(
            'AboutPageLocationsSeeder: обновлено локалей: %d, пропущено: %d.',
            $updated,
            $skipped
        ));
    }

    /**
     * Возвращает список локаций в форме, ожидаемой фронтендом
     * (`AboutGeoLocation` — поля lng/lat/labelOnRight/name).
     *
     * @return list<array{lng: float, lat: float, labelOnRight: bool, name: string}>
     */
    private function buildLocationsFor(string $locale): array
    {
        return array_map(
            static function (array $loc) use ($locale): array {
                $name = $loc['names'][$locale]
                    ?? $loc['names']['en']
                    ?? '';

                return [
                    'lng' => $loc['lng'],
                    'lat' => $loc['lat'],
                    'labelOnRight' => $loc['labelOnRight'],
                    'name' => $name,
                ];
            },
            self::LOCATIONS
        );
    }
}
