<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Form - Marine Technical Solutions</title>
    <style>
        @page {
            margin: 12mm;
            background-color: #ffffff;
        }
        /* Helvetica — встроенный sans-serif в PDF (DomPDF); Arial в браузере. */
        html {
            background-color: #ffffff;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #000000;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #ffffff;
        }
        .lang-ru {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
        }
        .user-text {
            font-family: Helvetica, Arial, DejaVu Sans, sans-serif;
        }
        .pdf-section-heading {
            font-family: Helvetica, Arial, sans-serif;
            font-style: normal;
            font-weight: 400;
            font-size: 24px;
            line-height: 29px;
            color: #000000;
        }
        h1.pdf-section-heading,
        h2.pdf-section-heading,
        p.pdf-section-heading.nok-title {
            font-weight: 400;
        }
        h1.pdf-section-heading {
            margin: 0 0 10px 0;
        }
        h2.pdf-section-heading {
            margin: 0 0 12px 0;
        }
        p.pdf-section-heading.nok-title {
            margin: 0 0 6px 0;
            text-transform: uppercase;
        }
        .pdf-banner-heading {
            font-family: Helvetica, Arial, sans-serif;
            font-style: normal;
            font-weight: 400;
            font-size: 11pt;
            line-height: 1.15;
            color: #000000;
        }
        th.pdf-banner-heading.table-header {
            font-weight: 400 !important;
            font-size: 11pt !important;
            line-height: 1.15 !important;
            padding: 3px 6px !important;
            vertical-align: middle;
        }
        .wrap {
            max-width: 210mm;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 10px 12px;
            box-sizing: border-box;
        }
        table { border-collapse: collapse; width: 100%; }
        .table-header {
            background-color: #b0b0b0;
            text-align: center;
            color: #000000;
        }
        .table-header-sub {
            background-color: #d8d8d8;
            color: #000000;
            font-weight: bold;
            text-align: center;
        }
        tr.table-header-sub th {
            background-color: #d8d8d8;
            color: #000000;
        }
        .cell-border, .cell-border th, .cell-border td {
            border: 1px solid #000000 !important;
        }
        .td-label {
            background-color: #d8d8d8;
            color: #000000;
            font-weight: bold;
        }
        .table-dark tbody td:not(.td-label) {
            font-family: Helvetica, Arial, DejaVu Sans, sans-serif;
        }
        .table-dark tbody td {
            background-color: #ffffff;
            color: #000000;
        }
        .table-dark tbody td.td-label {
            background-color: #d8d8d8;
            color: #000000;
            font-family: Helvetica, Arial, sans-serif;
        }
        .table-dark tbody td.empty-dash {
            color: #666666;
        }
        .table-dark tbody td {
            min-height: 34px;
        }
        .label-strip {
            background-color: #d8d8d8;
            color: #000000;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
            font-weight: bold;
            padding: 6pt 8pt;
            vertical-align: middle;
            line-height: 1.25;
            box-sizing: border-box;
            min-height: 40px;
        }
        .strip-value {
            background-color: #ffffff;
            color: #000000;
            font-size: 12px;
            padding: 6pt 8pt;
            vertical-align: middle;
            min-height: 40px;
            line-height: 1.3;
            box-sizing: border-box;
            font-family: Helvetica, Arial, DejaVu Sans, sans-serif;
        }
        .photo-box {
            border: 1px solid #000000;
            background-color: #ffffff;
            color: #000000;
            vertical-align: middle;
            text-align: center;
            font-size: 12px;
            font-family: Helvetica, Arial, sans-serif;
            width: 213px;
            min-width: 213px;
            max-width: 213px;
            min-height: 304px;
        }
        .consent { font-size: 9px; line-height: 1.25; margin: 0 0 10px 0; color: #000000; }
        .consent.consent-meta { font-size: 8px; color: #666666; }
        .sig { font-size: 11px; font-weight: bold; color: #000000; }
        .sig-line { border-bottom: 1px solid #000000; min-width: 180px; display: inline-block; }
        .footer-note { font-size: 9px; line-height: 1.25; color: #000000; }
        .footer-note p { color: inherit; }
        .mb-4 { margin-bottom: 24px; }
        .mb-6 { margin-bottom: 36px; }
        .mt-4 { margin-top: 24px; }
        .pdf-force-next-page {
            page-break-before: always;
            padding-top: 5mm;
        }
        .pdf-keep-together {
            page-break-inside: avoid;
        }
        .pdf-keep-together table {
            page-break-inside: avoid;
        }
        .text-right { text-align: right; }
        .logo-text { font-size: 11px; font-weight: bold; color: #000000; line-height: 1.1; }
        .form-title-row { margin-bottom: 12px; }
        .table-dark-head { padding: 0; }
        th.table-dark-sub {
            background-color: #d8d8d8;
            color: #000000;
        }
        .table-dark-sub {
            font-size: 12px;
            padding: 5pt 6pt;
            line-height: 1.25;
            font-family: Helvetica, Arial, sans-serif;
        }
        .table-dark-body {
            font-size: 12px;
            padding: 5pt 7pt;
            line-height: 1.3;
            vertical-align: top;
        }
    </style>
</head>
<body>
@php
    $travelLabels = ['Traveling passport', 'Civil passport', 'SBK', 'SID', 'Schengen visa', 'US VISA (C1/D)'];
    $competencyLabels = [
        'Certificate of Competency No (CoC)',
        'CoE Certificate of Endorsement No',
        'GMDSS Operator Certificate No (GOC)',
        'GOC Certificate of Endorsement No',
    ];
    $otherCertLabels = [
        'Basic Safety Training & Instruction<br>(Table A-VI/1-1)',
        'Proficiency in Survival craft & Rescue Boats<br>(Table A-VI/2-1)',
        'Advanced Fire Fighting<br>(Table A-VI/3)',
        'Medical First Aid (Table A-VI/4-1)',
        'Medical Care<br>(Table A-VI/4-2)',
        'Security training for seafarers with designated<br>security duties<br>(Table A-VI/6-2)',
        'Security Awareness Training<br>(Table A-VI/ 6-1)',
        'Operational use of automatic radar plotting<br>aids (ARPA)',
        'Radar observation and plotting (RADAR)',
        'ECDIS',
        'HAZMAT',
        'Bridge team and resource management',
        'Vaccination/YF',
        'Medical Health Certificate',
        'ISPS/SSO',
        'Preventing and responding to violence and harassment,<br>including sexual harassment, bullying,<br>and sexual violence',
        'Tanker certificates:',
    ];
@endphp

<div class="wrap">

    <table class="form-title-row" style="width:100%; table-layout:fixed;">
        <tr>
            <td style="vertical-align:bottom;">
                <h1 class="pdf-section-heading">Application form</h1>
            </td>
            <td class="text-right" style="vertical-align:top; width:247px;">
                @if($mtsLogoDataUri !== '')
                    <img src="{{ $mtsLogoDataUri }}" alt="Marine Technical Solutions" style="max-height:101px; width:auto; max-width:247px; display:inline-block;" />
                @else
                    <span class="logo-text">Marine Technical Solutions</span>
                @endif
            </td>
        </tr>
    </table>

    @php
        $pdfNameLine = trim((string) $surnameAndName) !== ''
            ? trim((string) $surnameAndName)
            : trim(trim((string) $lastName).' '.trim((string) $firstName));
        $mobileEmailLine = trim((string) $mobilePhone);
        $emailPart = trim((string) $email);
    @endphp

    <table class="mb-6 cell-border" style="width:100%; table-layout:fixed;">
        <colgroup>
            <col style="width:232px" />
            <col />
            <col style="width:213px" />
        </colgroup>
        <tr>
            <td class="cell-border label-strip" style="width:232px;">Position Applied For</td>
            <td class="cell-border strip-value" style="vertical-align:middle;">
                {{ $positionApplyingFor !== '' ? $positionApplyingFor : '—' }}
            </td>
            <td class="cell-border photo-box" rowspan="4">
                <div style="padding:10px;">
                    @if($photoDataUri !== '')
                        <img src="{{ $photoDataUri }}" alt="" style="max-width:190px; max-height:230px; display:inline-block;" />
                    @elseif($photoFileName !== '')
                        <div class="user-text" style="font-size:10px;">{{ $photoFileName }}</div>
                        <div style="font-size:8px; color:#444; margin-top:4px; line-height:1.2;"><span class="lang-ru">Файл фото не сохранён на сервере.</span><br />Photo file is not stored on the server.</div>
                    @else
                        <span>Photo</span>
                    @endif
                </div>
            </td>
        </tr>
        <tr>
            <td class="cell-border label-strip">Preferred Vessel Type</td>
            <td class="cell-border strip-value">{{ $desiredVesselTypesLine !== '' ? $desiredVesselTypesLine : '—' }}</td>
        </tr>
        <tr>
            <td class="cell-border label-strip">Last Name /<br />First Name</td>
            <td class="cell-border strip-value">{{ $pdfNameLine !== '' ? $pdfNameLine : '—' }}</td>
        </tr>
        <tr>
            <td class="cell-border label-strip">Mobile phone and<br />email</td>
            <td class="cell-border strip-value">
                @if($mobileEmailLine !== '')
                    {{ $mobileEmailLine }}@if($emailPart !== '')<br>{{ $emailPart }}@endif
                @elseif($emailPart !== '')
                    {{ $emailPart }}
                @else
                    —
                @endif
            </td>
        </tr>
    </table>

    <h2 class="pdf-section-heading">Main information</h2>
    <table class="mb-4 cell-border" style="width:100%; table-layout:fixed;">
        <colgroup>
            <col style="width:209px" />
            <col />
            <col style="width:209px" />
            <col />
        </colgroup>
        <tbody>
        <tr>
            <td class="cell-border label-strip">Date of birth/place <br />of birth</td>
            <td class="cell-border strip-value">
                @if(trim((string) $dateOfBirth) !== '')
                    {{ $dateOfBirth }}
                @endif
                @if(trim((string) $dateOfBirth) !== '' && trim((string) $placeOfBirth) !== '')
                    <span> / </span>
                @endif
                @if(trim((string) $placeOfBirth) !== '')
                    {{ $placeOfBirth }}
                @endif
                @if(trim((string) $dateOfBirth) === '' && trim((string) $placeOfBirth) === '')
                    —
                @endif
            </td>
            <td class="cell-border label-strip">Available from</td>
            <td class="cell-border strip-value">{{ trim((string) $availableFrom) !== '' ? $availableFrom : '—' }}</td>
        </tr>
        <tr>
            <td class="cell-border label-strip">Citizenship</td>
            <td class="cell-border strip-value">{{ trim((string) $citizenship) !== '' ? $citizenship : '—' }}</td>
            <td class="cell-border label-strip">Expected Monthly <br />Salary</td>
            <td class="cell-border strip-value">{{ trim((string) $expectedMonthlySalary) !== '' ? $expectedMonthlySalary.' '.$expectedMonthlySalaryCurrency : '—' }}</td>
        </tr>
        <tr>
            <td class="cell-border label-strip">Home address</td>
            <td class="cell-border strip-value">{{ trim((string) $homeAddress) !== '' ? $homeAddress : '—' }}</td>
            <td class="cell-border label-strip">Marital Status</td>
            <td class="cell-border strip-value">{{ trim((string) $maritalStatus) !== '' ? $maritalStatus : '—' }}</td>
        </tr>
        <tr>
            <td class="cell-border label-strip">Nearest airport</td>
            <td class="cell-border strip-value">{{ trim((string) $nearestAirport) !== '' ? $nearestAirport : '—' }}</td>
            <td class="cell-border label-strip">English level: basic/<br />medium/fluent</td>
            <td class="cell-border strip-value">{{ trim((string) $englishLevel) !== '' ? $englishLevel : '—' }}</td>
        </tr>
        </tbody>
    </table>

    {{-- Travel --}}
    <table class="cell-border mb-4 mt-4 table-dark" style="width:100%;">
        <thead>
        <tr>
            <th colspan="5" class="table-header cell-border table-dark-head pdf-banner-heading">TRAVEL DOCUMENT INFORMATION</th>
        </tr>
        <tr class="table-header-sub">
            <th class="cell-border table-dark-sub" style="width:28%;"></th>
            <th class="cell-border table-dark-sub">Number:</th>
            <th class="cell-border table-dark-sub">Place of issue:</th>
            <th class="cell-border table-dark-sub">Date of issue:</th>
            <th class="cell-border table-dark-sub">Date of expire:</th>
        </tr>
        </thead>
        <tbody>
        @foreach($travelFixed as $i => $c)
        <tr>
            <td class="cell-border td-label table-dark-body">{!! $travelLabels[$i] !!}</td>
            <td class="cell-border table-dark-body">{{ $c['number'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['placeOfIssue'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['dateOfIssue'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['dateOfExpire'] }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="5" class="cell-border td-label table-dark-body" style="text-align:center;">If you have any other travel documents, please provide the details below.</td>
        </tr>
        @foreach($travelOther as $c)
        <tr>
            <td class="cell-border table-dark-body">{{ $c['customLabel'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['number'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['placeOfIssue'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['dateOfIssue'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['dateOfExpire'] }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>

    {{-- Competency: с новой страницы --}}
    <div class="pdf-force-next-page">
    <table class="cell-border mb-4 table-dark" style="width:100%;">
        <thead>
        <tr>
            <th colspan="5" class="table-header cell-border table-dark-head pdf-banner-heading">COMPETENCY CERTIFICATES</th>
        </tr>
        <tr class="table-header-sub">
            <th class="cell-border table-dark-sub" style="width:28%;"></th>
            <th class="cell-border table-dark-sub">Number:</th>
            <th class="cell-border table-dark-sub">Place of issue:</th>
            <th class="cell-border table-dark-sub">Date of issue:</th>
            <th class="cell-border table-dark-sub">Date of expire:</th>
        </tr>
        </thead>
        <tbody>
        @foreach($competencyFixed as $i => $c)
        <tr>
            <td class="cell-border td-label table-dark-body">{!! $competencyLabels[$i] !!}</td>
            <td class="cell-border table-dark-body">{{ $c['number'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['placeOfIssue'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['dateOfIssue'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['dateOfExpire'] }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="5" class="cell-border td-label table-dark-body" style="text-align:center;">If you have any other competency certificates, please provide the details below.</td>
        </tr>
        @foreach($competencyOther as $c)
        <tr>
            <td class="cell-border table-dark-body">{{ $c['customLabel'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['number'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['placeOfIssue'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['dateOfIssue'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['dateOfExpire'] }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>

    {{-- Other certificates --}}
    <table class="cell-border mb-4 table-dark" style="width:100%;">
        <thead>
        <tr>
            <th colspan="5" class="table-header cell-border table-dark-head pdf-banner-heading">OTHER CERTIFICATES</th>
        </tr>
        <tr class="table-header-sub">
            <th class="cell-border table-dark-sub" style="width:28%;"></th>
            <th class="cell-border table-dark-sub">Number:</th>
            <th class="cell-border table-dark-sub">Place of issue:</th>
            <th class="cell-border table-dark-sub">Date of issue:</th>
            <th class="cell-border table-dark-sub">Date of expire:</th>
        </tr>
        </thead>
        <tbody>
        @foreach($otherCertFixed as $i => $c)
        <tr>
            <td class="cell-border td-label table-dark-body">{!! $otherCertLabels[$i] !!}</td>
            <td class="cell-border table-dark-body">{{ $c['number'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['placeOfIssue'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['dateOfIssue'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['dateOfExpire'] }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="5" class="cell-border td-label table-dark-body" style="text-align:center;">If you have any other certificates, please provide the details below.</td>
        </tr>
        @foreach($otherCertOther as $c)
        <tr>
            <td class="cell-border table-dark-body">{{ $c['customLabel'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['number'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['placeOfIssue'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['dateOfIssue'] }}</td>
            <td class="cell-border table-dark-body">{{ $c['dateOfExpire'] }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>

    {{-- Sea service --}}
    <table class="cell-border mb-4 table-dark" style="width:100%;">
        <thead>
        <tr>
            <th colspan="9" class="table-header cell-border table-dark-head pdf-banner-heading">PREVIOUS SEA SERVICE (FOR THE LAST 10 YEARS)</th>
        </tr>
        <tr class="table-header-sub">
            <th class="cell-border table-dark-sub">Rank:</th>
            <th class="cell-border table-dark-sub">Company:</th>
            <th class="cell-border table-dark-sub">Flag:</th>
            <th class="cell-border table-dark-sub">Name of<br>vessel:</th>
            <th class="cell-border table-dark-sub">GRT/DWT:</th>
            <th class="cell-border table-dark-sub">Vessel<br>type:</th>
            <th class="cell-border table-dark-sub">Engine/<br>kW:</th>
            <th class="cell-border table-dark-sub">Date sign<br>on:</th>
            <th class="cell-border table-dark-sub">Date sign<br>off:</th>
        </tr>
        </thead>
        <tbody>
        @forelse($seaService as $r)
        <tr>
            <td class="cell-border table-dark-body">{{ $r['rank'] }}</td>
            <td class="cell-border table-dark-body">{{ $r['company'] }}</td>
            <td class="cell-border table-dark-body">{{ $r['flag'] }}</td>
            <td class="cell-border table-dark-body">{{ $r['vesselName'] }}</td>
            <td class="cell-border table-dark-body">{{ $r['grtDwt'] }}</td>
            <td class="cell-border table-dark-body">{{ $r['vesselType'] }}</td>
            <td class="cell-border table-dark-body">{{ $r['engineKw'] }}</td>
            <td class="cell-border table-dark-body">{{ $r['signOn'] }}</td>
            <td class="cell-border table-dark-body">{{ $r['signOff'] }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="cell-border table-dark-body empty-dash">—</td>
        </tr>
        @endforelse
        </tbody>
    </table>

    {{-- Education --}}
    <table class="cell-border mb-4 table-dark" style="width:100%;">
        <thead>
        <tr>
            <th colspan="4" class="table-header cell-border table-dark-head pdf-banner-heading">EDUCATION</th>
        </tr>
        <tr class="table-header-sub">
            <th class="cell-border table-dark-sub">Name of school:</th>
            <th class="cell-border table-dark-sub">From:</th>
            <th class="cell-border table-dark-sub">Till:</th>
            <th class="cell-border table-dark-sub">Type of degree:</th>
        </tr>
        </thead>
        <tbody>
        @forelse($education as $e)
        <tr>
            <td class="cell-border table-dark-body">{{ $e['schoolName'] }}</td>
            <td class="cell-border table-dark-body">{{ $e['from'] }}</td>
            <td class="cell-border table-dark-body">{{ $e['till'] }}</td>
            <td class="cell-border table-dark-body">{{ $e['degreeType'] }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="cell-border table-dark-body empty-dash">—</td>
        </tr>
        @endforelse
        </tbody>
    </table>

    <table class="mb-4 cell-border" style="width:100%; table-layout:fixed;">
        <thead>
        <tr>
            <th colspan="4" class="table-header cell-border table-dark-head pdf-banner-heading">NEXT OF KIN (NOK) DETAILS - PERSON CONTACTED<br>IN CASE OF EMERGENCY</th>
        </tr>
        </thead>
        <colgroup>
            <col style="width:124px" />
            <col />
            <col style="width:165px" />
            <col />
        </colgroup>
        <tr>
            <td class="cell-border label-strip">Last name</td>
            <td class="cell-border strip-value">{{ trim((string) $nokLastName) !== '' ? $nokLastName : '—' }}</td>
            <td class="cell-border label-strip">NOK contact <br />number</td>
            <td class="cell-border strip-value">{{ trim((string) $nokContactNumber) !== '' ? $nokContactNumber : '—' }}</td>
        </tr>
        <tr>
            <td class="cell-border label-strip">First name</td>
            <td class="cell-border strip-value">{{ trim((string) $nokFirstName) !== '' ? $nokFirstName : '—' }}</td>
            <td class="cell-border label-strip">E-mail address</td>
            <td class="cell-border strip-value">{{ trim((string) $nokEmail) !== '' ? $nokEmail : '—' }}</td>
        </tr>
        <tr>
            <td class="cell-border label-strip" style="width:305px;">Relationship (spouse, mother,<br />father, etc..)</td>
            <td class="cell-border strip-value" colspan="3">{{ trim((string) $nokRelationship) !== '' ? $nokRelationship : '—' }}</td>
        </tr>
        <tr>
            <td class="cell-border label-strip" style="width:305px;">NOK address</td>
            <td class="cell-border strip-value" colspan="3">{{ trim((string) $nokAddress) !== '' ? $nokAddress : '—' }}</td>
        </tr>
    </table>

    {{-- Safety clothes: целиком на одной странице (баннер + строки) --}}
    <div class="pdf-keep-together">
    <table class="cell-border mb-4 table-dark" style="width:100%;">
        <thead>
        <tr>
            <th colspan="4" class="table-header cell-border table-dark-head pdf-banner-heading">SAFETY CLOTHES</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="cell-border td-label table-dark-body" style="width:25%;">Overall size:</td>
            <td class="cell-border table-dark-body" style="width:25%;">{{ $safetyOverallSize }}</td>
            <td class="cell-border td-label table-dark-body" style="width:25%;">Height:</td>
            <td class="cell-border table-dark-body" style="width:25%;">{{ $safetyHeight }}</td>
        </tr>
        <tr>
            <td class="cell-border td-label table-dark-body">Shoe size:</td>
            <td class="cell-border table-dark-body">{{ $safetyShoeSize }}</td>
            <td class="cell-border td-label table-dark-body">Weight:</td>
            <td class="cell-border table-dark-body">{{ $safetyWeight }}</td>
        </tr>
        </tbody>
    </table>
    </div>

    <p class="consent lang-ru">
        Настоящим я подтверждаю, что все данные, насколько они были мне известны, точны и верны и что у меня имеются законным порядком приобретенные квалификация, рабочий диплом и сертификаты. Я также подтверждаю, что я не являюсь объектом уголовных преследований и не имею никаких финансовых задолженностей перед третьими лицами.
    </p>
    <p class="consent lang-ru">
        Отправляя анкету, я предоставляю согласие ООО «Марин Техникал Солюшионс» на обработку, включая сбор, запись, систематизацию, накопление, хранение, уточнение, извлечение, использование, передачу (предоставление, доступ), обезличивание, блокирование, удаление, уничтожение, своих персональных данных в документальной и/или электронной форме в соответствии со ст. 9 Федерального закона от 27.07.2006 № 152-ФЗ «О персональных данных».
    </p>

    <p class="consent">
        I confirm that the details given are to the best of my knowledge accurate and true, that I am in legal possession of the above qualifications and certificates. I also confirm that I have no unspent criminal convictions and I have no financial debts to third parties.
    </p>
    <p class="consent mb-4">
        By submitting the application form, I give my consent to LLC "Marine Technical Solutions" for the processing of personal data, including collection, recording, systematization, accumulation, storage, clarification, extraction, use, transfer (provision, access), depersonalization, blocking, deletion, destruction, of my personal data in documentary and/or electronic form in accordance with Article 9 of Federal Law No. 152-FZ dated 07/27/2006 "On Personal Data".
    </p>

    <p class="consent consent-meta">
        @php
            $y = static fn (bool $v): string => $v ? 'yes' : 'no';
        @endphp
        Consents (submitted online): RU accuracy — {{ $y($consentRuAccuracy) }}; RU PD — {{ $y($consentRuPd) }}; EN accuracy — {{ $y($consentEnAccuracy) }}; EN PD — {{ $y($consentEnPd) }}.
    </p>

    <table class="mb-6" style="width:100%;">
        <tr>
            <td style="width:70%;">
                <span class="sig">APPLICANT'S SIGNATURE:</span>
                <span class="sig-line">&nbsp;</span>
            </td>
            <td style="width:30%;">
                <span class="sig">DATE:</span>
                <span class="sig-line" style="min-width:80px;">{{ $pdfSubmissionDate }}</span>
            </td>
        </tr>
    </table>

    <table style="width:100%;">
        <tr>
            <td class="footer-note" style="vertical-align:top;">
                <p class="lang-ru" style="margin:0;">Пожалуйста, отправьте эту анкету на адрес</p>
                <p style="margin:0;">Please send this application to</p>
            </td>
            <td class="footer-note" style="font-weight:bold; vertical-align:top;">
                <p style="margin:0;">crewing@marin-ts.com</p>
            </td>
        </tr>
    </table>

</div>
</body>
</html>
