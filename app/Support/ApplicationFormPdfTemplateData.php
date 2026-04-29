<?php

namespace App\Support;

use App\Models\ApplicationForm;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Данные для PDF-шаблона анкеты (структура как на сайте / HTML-шаблон).
 *
 * @phpstan-type CertCells array{number: string, placeOfIssue: string, dateOfIssue: string, dateOfExpire: string}
 * @phpstan-type CertCellsExtra array{customLabel: string, number: string, placeOfIssue: string, dateOfIssue: string, dateOfExpire: string}
 */
final class ApplicationFormPdfTemplateData
{
    /**
     * @return array<string, mixed>
     */
    public static function make(ApplicationForm $form): array
    {
        $form->loadMissing('vacancy');
        $p = is_array($form->payload) ? $form->payload : [];

        $travelLabelRows = self::listFrom($p, 'travelRows');
        $competencyLabelRows = self::listFrom($p, 'competencyRows');
        $otherCertLabelRows = self::listFrom($p, 'otherCertificateRows');

        return [
            'form' => $form,
            'positionApplyingFor' => self::strFrom($p, 'positionApplyingFor'),
            'surnameAndName' => self::strFrom($p, 'surnameAndName'),
            'dateOfBirth' => self::strFrom($p, 'dateOfBirth'),
            'photoFileName' => self::strFrom($p, 'photoFileName'),
            'lastName' => self::strFrom($p, 'lastName'),
            'firstName' => self::strFrom($p, 'firstName'),
            'fathersName' => self::strFrom($p, 'fathersName'),
            'maritalStatus' => self::strFrom($p, 'maritalStatus'),
            'placeOfBirth' => self::strFrom($p, 'placeOfBirth'),
            'availableFrom' => self::strFrom($p, 'availableFrom'),
            'citizenship' => self::strFrom($p, 'citizenship'),
            'englishLevel' => self::strFrom($p, 'englishLevel'),
            'mobilePhone' => self::strFrom($p, 'mobilePhone'),
            'homePhone' => self::strFrom($p, 'homePhone'),
            'email' => self::strFrom($p, 'email'),
            'messenger' => self::strFrom($p, 'messenger'),
            'homeAddress' => self::strFrom($p, 'homeAddress'),
            'nearestAirport' => self::strFrom($p, 'nearestAirport'),
            'nokLastName' => self::strFrom($p, 'nokLastName'),
            'nokFirstName' => self::strFrom($p, 'nokFirstName'),
            'nokContactNumber' => self::strFrom($p, 'nokContactNumber'),
            'nokEmail' => self::strFrom($p, 'nokEmail'),
            'nokRelationship' => self::strFrom($p, 'nokRelationship'),
            'nokAddress' => self::strFrom($p, 'nokAddress'),
            'travelFixed' => self::mapFixedTravel($travelLabelRows),
            'travelOther' => self::mapTravelOther(self::pick($p, 'travelOtherRows')),
            'competencyFixed' => self::mapFixedCompetency($competencyLabelRows),
            'competencyOther' => self::mapCompetencyOther(self::pick($p, 'competencyOtherRows')),
            'otherCertFixed' => self::mapFixedOtherCerts($otherCertLabelRows),
            'otherCertOther' => self::mapOtherCertExtra(self::pick($p, 'otherCertificateExtraRows')),
            'seaService' => self::mapSeaService(self::pick($p, 'seaServiceRows')),
            'education' => self::mapEducation(self::pick($p, 'educationRows')),
            'safetyOverallSize' => self::strFrom($p, 'safetyOverallSize'),
            'safetyHeight' => self::strFrom($p, 'safetyHeight'),
            'safetyShoeSize' => self::strFrom($p, 'safetyShoeSize'),
            'safetyWeight' => self::strFrom($p, 'safetyWeight'),
            'consentRuAccuracy' => self::boolVal(self::pick($p, 'consentRuAccuracy')),
            'consentRuPd' => self::boolVal(self::pick($p, 'consentRuPd')),
            'consentEnAccuracy' => self::boolVal(self::pick($p, 'consentEnAccuracy')),
            'consentEnPd' => self::boolVal(self::pick($p, 'consentEnPd')),
            'mtsLogoDataUri' => self::mtsLogoDataUri(),
            'photoDataUri' => self::photoDataUri($p),
        ];
    }

    /**
     * Конвертирует загруженное фото из disk `local` в data URI для DomPDF.
     *
     * @param  array<string, mixed>  $payload
     */
    private static function photoDataUri(array $payload): string
    {
        $path = self::pick($payload, 'photoStoredPath');
        if (! is_string($path) || $path === '') {
            return '';
        }

        $disk = Storage::disk('local');
        if (! $disk->exists($path)) {
            return '';
        }

        $raw = $disk->get($path);
        if (! is_string($raw) || $raw === '') {
            return '';
        }

        $mime = self::pick($payload, 'photoMime');
        if (! is_string($mime) || $mime === '') {
            $mime = self::guessMimeByExtension($path);
        }

        return 'data:'.$mime.';base64,'.base64_encode($raw);
    }

    private static function guessMimeByExtension(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($ext) {
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            default => 'image/jpeg',
        };
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function pick(array $data, string $camelKey): mixed
    {
        if (array_key_exists($camelKey, $data)) {
            return $data[$camelKey];
        }

        return $data[Str::snake($camelKey)] ?? null;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function strFrom(array $data, string $camelKey): string
    {
        return self::scalar(self::pick($data, $camelKey));
    }

    /**
     * @param  array<string, mixed>  $p
     * @return list<array<string, mixed>>
     */
    private static function listFrom(array $p, string $camelKey): array
    {
        $v = self::pick($p, $camelKey);

        return is_array($v) ? $v : [];
    }

    /**
     * JPEG в data: URI для DomPDF (локальный файл без сети).
     */
    private static function mtsLogoDataUri(): string
    {
        $path = storage_path('app/public/images/mts-logo.jpg');
        if (! is_readable($path)) {
            return '';
        }
        $raw = @file_get_contents($path);
        if ($raw === false || $raw === '') {
            return '';
        }

        return 'data:image/jpeg;base64,'.base64_encode($raw);
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<CertCells>
     */
    private static function mapFixedTravel(array $rows): array
    {
        $out = [];
        for ($i = 0; $i < 6; $i++) {
            $out[] = self::cellsFromLabelRow($rows[$i] ?? null);
        }

        return $out;
    }

    /**
     * Доп. строки «travel other» — столько, сколько в анкете (динамически).
     *
     * @return list<CertCellsExtra>
     */
    private static function mapTravelOther(mixed $rows): array
    {
        $list = is_array($rows) ? $rows : [];
        $out = [];
        foreach ($list as $row) {
            $out[] = self::cellsFromPlainCertExtra($row);
        }

        return $out;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<CertCells>
     */
    private static function mapFixedCompetency(array $rows): array
    {
        $out = [];
        for ($i = 0; $i < 4; $i++) {
            $out[] = self::cellsFromLabelRow($rows[$i] ?? null);
        }

        return $out;
    }

    /**
     * @return list<CertCellsExtra>
     */
    private static function mapCompetencyOther(mixed $rows): array
    {
        $list = is_array($rows) ? $rows : [];
        $out = [];
        foreach ($list as $row) {
            $out[] = self::cellsFromPlainCertExtra($row);
        }

        return $out;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<CertCells>
     */
    private static function mapFixedOtherCerts(array $rows): array
    {
        $out = [];
        for ($i = 0; $i < 15; $i++) {
            $out[] = self::cellsFromLabelRow($rows[$i] ?? null);
        }

        return $out;
    }

    /**
     * @return list<CertCellsExtra>
     */
    private static function mapOtherCertExtra(mixed $rows): array
    {
        $list = is_array($rows) ? $rows : [];
        $out = [];
        foreach ($list as $row) {
            $out[] = self::cellsFromPlainCertExtra($row);
        }

        return $out;
    }

    /**
     * @return list<array{rank: string, company: string, flag: string, vesselName: string, grtDwt: string, vesselType: string, engineKw: string, signOn: string, signOff: string}>
     */
    private static function mapSeaService(mixed $rows): array
    {
        $list = is_array($rows) ? $rows : [];
        $out = [];
        foreach ($list as $r) {
            if (! is_array($r)) {
                continue;
            }
            $out[] = [
                'rank' => self::strFrom($r, 'rank'),
                'company' => self::strFrom($r, 'company'),
                'flag' => self::strFrom($r, 'flag'),
                'vesselName' => self::strFrom($r, 'vesselName'),
                'grtDwt' => self::strFrom($r, 'grtDwt'),
                'vesselType' => self::strFrom($r, 'vesselType'),
                'engineKw' => self::strFrom($r, 'engineKw'),
                'signOn' => self::strFrom($r, 'signOn'),
                'signOff' => self::strFrom($r, 'signOff'),
            ];
        }

        return $out;
    }

    /**
     * @return list<array{schoolName: string, from: string, till: string, degreeType: string}>
     */
    private static function mapEducation(mixed $rows): array
    {
        $list = is_array($rows) ? $rows : [];
        $out = [];
        foreach ($list as $r) {
            if (! is_array($r)) {
                continue;
            }
            $out[] = [
                'schoolName' => self::strFrom($r, 'schoolName'),
                'from' => self::strFrom($r, 'from'),
                'till' => self::strFrom($r, 'till'),
                'degreeType' => self::strFrom($r, 'degreeType'),
            ];
        }

        return $out;
    }

    /**
     * @return CertCells
     */
    private static function cellsFromLabelRow(mixed $row): array
    {
        if (! is_array($row)) {
            return self::emptyCert();
        }
        $data = $row['data'] ?? null;

        return self::cellsFromPlainCert(is_array($data) ? $data : null);
    }

    /**
     * @return CertCells
     */
    private static function cellsFromPlainCert(mixed $row): array
    {
        if (! is_array($row)) {
            return self::emptyCert();
        }

        return [
            'number' => self::strFrom($row, 'number'),
            'placeOfIssue' => self::strFrom($row, 'placeOfIssue'),
            'dateOfIssue' => self::strFrom($row, 'dateOfIssue'),
            'dateOfExpire' => self::strFrom($row, 'dateOfExpire'),
        ];
    }

    /**
     * @return CertCellsExtra
     */
    private static function cellsFromPlainCertExtra(mixed $row): array
    {
        $c = self::cellsFromPlainCert($row);
        $custom = is_array($row) ? self::strFrom($row, 'customLabel') : '';

        return array_merge(['customLabel' => $custom], $c);
    }

    /**
     * @return CertCells
     */
    private static function emptyCert(): array
    {
        return [
            'number' => '',
            'placeOfIssue' => '',
            'dateOfIssue' => '',
            'dateOfExpire' => '',
        ];
    }

    private static function scalar(mixed $v): string
    {
        if ($v === null) {
            return '';
        }
        if (is_bool($v)) {
            return $v ? '1' : '';
        }
        if (is_scalar($v)) {
            return trim((string) $v);
        }

        return '';
    }

    private static function boolVal(mixed $v): bool
    {
        return $v === true || $v === 1 || $v === '1';
    }
}
