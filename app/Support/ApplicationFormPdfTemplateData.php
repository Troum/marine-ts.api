<?php

namespace App\Support;

use App\Models\ApplicationForm;

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

        $travelLabelRows = self::listOrEmpty($p['travelRows'] ?? null);
        $competencyLabelRows = self::listOrEmpty($p['competencyRows'] ?? null);
        $otherCertLabelRows = self::listOrEmpty($p['otherCertificateRows'] ?? null);

        return [
            'form' => $form,
            'positionApplyingFor' => self::scalar($p['positionApplyingFor'] ?? ''),
            'surnameAndName' => self::scalar($p['surnameAndName'] ?? ''),
            'dateOfBirth' => self::scalar($p['dateOfBirth'] ?? ''),
            'photoFileName' => self::scalar($p['photoFileName'] ?? ''),
            'lastName' => self::scalar($p['lastName'] ?? ''),
            'firstName' => self::scalar($p['firstName'] ?? ''),
            'fathersName' => self::scalar($p['fathersName'] ?? ''),
            'maritalStatus' => self::scalar($p['maritalStatus'] ?? ''),
            'placeOfBirth' => self::scalar($p['placeOfBirth'] ?? ''),
            'availableFrom' => self::scalar($p['availableFrom'] ?? ''),
            'citizenship' => self::scalar($p['citizenship'] ?? ''),
            'englishLevel' => self::scalar($p['englishLevel'] ?? ''),
            'mobilePhone' => self::scalar($p['mobilePhone'] ?? ''),
            'homePhone' => self::scalar($p['homePhone'] ?? ''),
            'email' => self::scalar($p['email'] ?? ''),
            'messenger' => self::scalar($p['messenger'] ?? ''),
            'homeAddress' => self::scalar($p['homeAddress'] ?? ''),
            'nearestAirport' => self::scalar($p['nearestAirport'] ?? ''),
            'nokLastName' => self::scalar($p['nokLastName'] ?? ''),
            'nokFirstName' => self::scalar($p['nokFirstName'] ?? ''),
            'nokContactNumber' => self::scalar($p['nokContactNumber'] ?? ''),
            'nokEmail' => self::scalar($p['nokEmail'] ?? ''),
            'nokRelationship' => self::scalar($p['nokRelationship'] ?? ''),
            'nokAddress' => self::scalar($p['nokAddress'] ?? ''),
            'travelFixed' => self::mapFixedTravel($travelLabelRows),
            'travelOther' => self::mapTravelOther($p['travelOtherRows'] ?? null),
            'competencyFixed' => self::mapFixedCompetency($competencyLabelRows),
            'competencyOther' => self::mapCompetencyOther($p['competencyOtherRows'] ?? null),
            'otherCertFixed' => self::mapFixedOtherCerts($otherCertLabelRows),
            'otherCertOther' => self::mapOtherCertExtra($p['otherCertificateExtraRows'] ?? null),
            'seaService' => self::mapSeaService($p['seaServiceRows'] ?? null),
            'education' => self::mapEducation($p['educationRows'] ?? null),
            'safetyOverallSize' => self::scalar($p['safetyOverallSize'] ?? ''),
            'safetyHeight' => self::scalar($p['safetyHeight'] ?? ''),
            'safetyShoeSize' => self::scalar($p['safetyShoeSize'] ?? ''),
            'safetyWeight' => self::scalar($p['safetyWeight'] ?? ''),
            'consentRuAccuracy' => self::boolVal($p['consentRuAccuracy'] ?? false),
            'consentRuPd' => self::boolVal($p['consentRuPd'] ?? false),
            'consentEnAccuracy' => self::boolVal($p['consentEnAccuracy'] ?? false),
            'consentEnPd' => self::boolVal($p['consentEnPd'] ?? false),
            'mtsLogoDataUri' => self::mtsLogoDataUri(),
        ];
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
                'rank' => self::scalar($r['rank'] ?? ''),
                'company' => self::scalar($r['company'] ?? ''),
                'flag' => self::scalar($r['flag'] ?? ''),
                'vesselName' => self::scalar($r['vesselName'] ?? ''),
                'grtDwt' => self::scalar($r['grtDwt'] ?? ''),
                'vesselType' => self::scalar($r['vesselType'] ?? ''),
                'engineKw' => self::scalar($r['engineKw'] ?? ''),
                'signOn' => self::scalar($r['signOn'] ?? ''),
                'signOff' => self::scalar($r['signOff'] ?? ''),
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
                'schoolName' => self::scalar($r['schoolName'] ?? ''),
                'from' => self::scalar($r['from'] ?? ''),
                'till' => self::scalar($r['till'] ?? ''),
                'degreeType' => self::scalar($r['degreeType'] ?? ''),
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
            'number' => self::scalar($row['number'] ?? ''),
            'placeOfIssue' => self::scalar($row['placeOfIssue'] ?? ''),
            'dateOfIssue' => self::scalar($row['dateOfIssue'] ?? ''),
            'dateOfExpire' => self::scalar($row['dateOfExpire'] ?? ''),
        ];
    }

    /**
     * @return CertCellsExtra
     */
    private static function cellsFromPlainCertExtra(mixed $row): array
    {
        $c = self::cellsFromPlainCert($row);
        $custom = '';
        if (is_array($row)) {
            $custom = self::scalar($row['customLabel'] ?? '');
        }

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

    /**
     * @return list<array<string, mixed>>
     */
    private static function listOrEmpty(mixed $v): array
    {
        return is_array($v) ? $v : [];
    }
}
