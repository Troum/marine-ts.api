<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Form - Marine Technical Solutions</title>
    <style>
        @page { margin: 12mm; }
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .wrap { max-width: 210mm; margin: 0 auto; }
        table { border-collapse: collapse; width: 100%; }
        .table-header {
            background-color: #404040;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }
        .table-header-sub {
            background-color: #808080;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }
        .cell-border, .cell-border th, .cell-border td {
            border: 1px solid #000 !important;
        }
        .td-label {
            background-color: #d1d5db;
            font-weight: bold;
        }
        .field-label { font-size: 10px; }
        .field-value { font-size: 11px; margin-top: 2px; min-height: 14px; }
        .photo-box {
            border: 1px solid #000;
            vertical-align: middle;
            text-align: center;
            font-size: 10px;
            width: 128px;
        }
        h1 { font-size: 1.25rem; font-weight: bold; margin: 0; }
        h2 { font-size: 10px; font-weight: bold; margin: 0 0 8px 0; }
        .nok-title { font-size: 10px; font-weight: bold; text-transform: uppercase; margin: 0 0 8px 0; }
        .consent { font-size: 10px; line-height: 1.25; margin: 0 0 8px 0; }
        .sig { font-size: 11px; font-weight: bold; }
        .sig-line { border-bottom: 1px solid #000; min-width: 180px; display: inline-block; }
        .footer-note { font-size: 10px; line-height: 1.25; }
        .mb-4 { margin-bottom: 16px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-6 { margin-bottom: 24px; }
        .text-right { text-align: right; }
        .logo-text { font-size: 12px; font-weight: bold; color: #dc2626; line-height: 1.1; }
    </style>
</head>
<body>
@php
    $travelLabels = ['Travelling passport', 'Civil passport', 'SBK', 'SID', 'Schengen visa', 'US VISA (C1/D)'];
    $competencyLabels = [
        'Certificate of Competency № (CoC)',
        'CoC Certificate of Endorsement №',
        'GMDSS Operator Certificate № (GOC)',
        'GOC Certificate of Endorsement №',
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
        'Tanker certificates:',
    ];
@endphp

<div class="wrap">

    <table class="mb-4" style="width:100%;">
        <tr>
            <td style="vertical-align:top;">
                <h1>Application form</h1>
            </td>
            <td class="text-right" style="vertical-align:top;">
                @if($mtsLogoDataUri !== '')
                    <img src="{{ $mtsLogoDataUri }}" alt="Marine Technical Solutions" style="max-height:44px; width:auto; display:inline-block;" />
                @else
                    <span class="logo-text">Marine Technical Solutions</span>
                @endif
            </td>
        </tr>
    </table>

    <table class="mb-4 cell-border" style="width:100%;">
        <tr>
            <td style="width:75%; vertical-align:top; padding:0;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td class="cell-border" style="padding:8px; min-height:50px;">
                            <div class="field-label">Position applying for:</div>
                            <div class="field-value">{{ $positionApplyingFor }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="cell-border" style="padding:8px; min-height:50px;">
                            <div class="field-label">Desired vessel type(s):</div>
                            <div class="field-value">{{ $desiredVesselTypesLine !== '' ? $desiredVesselTypesLine : '—' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="cell-border" style="padding:8px; min-height:50px;">
                            <div class="field-label">Surname and name:</div>
                            <div class="field-value">{{ $surnameAndName }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="cell-border" style="padding:8px; min-height:50px;">
                            <div class="field-label">Date of birth:</div>
                            <div class="field-value">{{ $dateOfBirth }}</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="photo-box" style="width:25%;">
                @if($photoDataUri !== '')
                    <img src="{{ $photoDataUri }}" alt="Photo" style="max-width:120px; max-height:160px; display:inline-block;" />
                @elseif($photoFileName !== '')
                    <div>{{ $photoFileName }}</div>
                    <div style="font-size:8px; color:#444; margin-top:6px; line-height:1.2;">Файл фото не сохранён на сервере.<br />Photo file is not stored on the server.</div>
                @else
                    <span>Photo</span>
                @endif
            </td>
        </tr>
    </table>

    <h2 class="mb-2">Main information:</h2>
    <table class="mb-4 cell-border" style="width:100%;">
        <tr>
            <td class="cell-border" style="width:50%; padding:8px;">
                <div class="field-label">Last name/ Surname:</div>
                <div class="field-value">{{ $lastName }}</div>
            </td>
            <td class="cell-border" style="width:50%; padding:8px;">
                <div class="field-label">Marital Status:</div>
                <div class="field-value">{{ $maritalStatus }}</div>
            </td>
        </tr>
        <tr>
            <td class="cell-border" style="padding:8px;">
                <div class="field-label">First name:</div>
                <div class="field-value">{{ $firstName }}</div>
            </td>
            <td class="cell-border" style="padding:8px;">
                <div class="field-label">Place of birth:</div>
                <div class="field-value">{{ $placeOfBirth }}</div>
            </td>
        </tr>
        <tr>
            <td class="cell-border" style="padding:8px;">
                <div class="field-label">Father's name:</div>
                <div class="field-value">{{ $fathersName }}</div>
            </td>
            <td class="cell-border" style="padding:8px;">
                <div class="field-label">Available from:</div>
                <div class="field-value">{{ $availableFrom }}</div>
            </td>
        </tr>
        <tr>
            <td class="cell-border" style="padding:8px;">
                <div class="field-label">Citizenship:</div>
                <div class="field-value">{{ $citizenship }}</div>
            </td>
            <td class="cell-border" style="padding:8px;">
                <div class="field-label">Mobile phone:</div>
                <div class="field-value">{{ $mobilePhone }}</div>
            </td>
        </tr>
        <tr>
            <td class="cell-border" style="padding:8px;">
                <div class="field-label">English level:</div>
                <div class="field-label">basic/medium/fluent:</div>
                <div class="field-value">{{ $englishLevel }}</div>
            </td>
            <td class="cell-border" style="padding:8px;">
                <div class="field-label">Home phone:</div>
                <div class="field-value">{{ $homePhone }}</div>
            </td>
        </tr>
        <tr>
            <td class="cell-border" style="padding:8px;">
                <div class="field-label">E-mail:</div>
                <div class="field-value">{{ $email }}</div>
            </td>
            <td class="cell-border" style="padding:8px;">
                <div class="field-label">Messenger: WhatsApp/Telegram, etc..</div>
                <div class="field-value">{{ $messenger }}</div>
            </td>
        </tr>
    </table>

    <table class="mb-4 cell-border" style="width:100%;">
        <tr>
            <td class="cell-border" style="padding:8px;">
                <div class="field-label">Complete home address:</div>
                <div class="field-value">{{ $homeAddress }}</div>
            </td>
        </tr>
        <tr>
            <td class="cell-border" style="padding:8px;">
                <div class="field-label">Nearest airport:</div>
                <div class="field-value">{{ $nearestAirport }}</div>
            </td>
        </tr>
    </table>

    <p class="nok-title">NEXT OF KIN (NOK) DETAILS - PERSON CONTACTED<br>IN CASE OF EMERGENCY</p>
    <table class="mb-4 cell-border" style="width:100%;">
        <tr>
            <td class="cell-border" style="width:50%; padding:8px;">
                <div class="field-label">Last name:</div>
                <div class="field-value">{{ $nokLastName }}</div>
            </td>
            <td class="cell-border" style="width:50%; padding:8px;">
                <div class="field-label">NOK contact number:</div>
                <div class="field-value">{{ $nokContactNumber }}</div>
            </td>
        </tr>
        <tr>
            <td class="cell-border" style="padding:8px;">
                <div class="field-label">First name:</div>
                <div class="field-value">{{ $nokFirstName }}</div>
            </td>
            <td class="cell-border" style="padding:8px;">
                <div class="field-label">E-mail address:</div>
                <div class="field-value">{{ $nokEmail }}</div>
            </td>
        </tr>
    </table>
    <table class="mb-4 cell-border" style="width:100%;">
        <tr>
            <td class="cell-border" style="padding:8px;">
                <div class="field-label">Relationship (spouse, mother,father, etc..):</div>
                <div class="field-value">{{ $nokRelationship }}</div>
            </td>
        </tr>
        <tr>
            <td class="cell-border" style="padding:8px;">
                <div class="field-label">NOK address:</div>
                <div class="field-value">{{ $nokAddress }}</div>
            </td>
        </tr>
    </table>

    {{-- Travel --}}
    <table class="cell-border mb-4" style="width:100%;">
        <thead>
        <tr>
            <th colspan="5" class="table-header cell-border" style="font-size:10px; padding:4px;">TRAVEL DOCUMENT INFORMATION</th>
        </tr>
        <tr class="table-header-sub">
            <th class="cell-border" style="padding:4px;"></th>
            <th class="cell-border" style="padding:4px; font-size:10px;">Number:</th>
            <th class="cell-border" style="padding:4px; font-size:10px;">Place of issue:</th>
            <th class="cell-border" style="padding:4px; font-size:10px;">Date of issue:</th>
            <th class="cell-border" style="padding:4px; font-size:10px;">Date of expire:</th>
        </tr>
        </thead>
        <tbody style="font-size:10px;">
        @foreach($travelFixed as $i => $c)
        <tr>
            <td class="cell-border td-label" style="padding:4px;">{{ $travelLabels[$i] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $c['number'] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $c['placeOfIssue'] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $c['dateOfIssue'] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $c['dateOfExpire'] }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="5" class="cell-border td-label" style="text-align:center; padding:4px;">If you have any other travel documents, please provide the details below.</td>
        </tr>
        @foreach($travelOther as $c)
        <tr>
            <td class="cell-border" style="padding:8px;">{{ $c['customLabel'] }}</td>
            <td class="cell-border" style="padding:8px;">{{ $c['number'] }}</td>
            <td class="cell-border" style="padding:8px;">{{ $c['placeOfIssue'] }}</td>
            <td class="cell-border" style="padding:8px;">{{ $c['dateOfIssue'] }}</td>
            <td class="cell-border" style="padding:8px;">{{ $c['dateOfExpire'] }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>

    {{-- Competency --}}
    <table class="cell-border mb-4" style="width:100%;">
        <thead>
        <tr>
            <th colspan="5" class="table-header cell-border" style="font-size:10px; padding:4px;">COMPETENCY CERTIFICATES</th>
        </tr>
        <tr class="table-header-sub">
            <th class="cell-border" style="padding:4px;"></th>
            <th class="cell-border" style="padding:4px; font-size:10px;">Number:</th>
            <th class="cell-border" style="padding:4px; font-size:10px;">Place of issue:</th>
            <th class="cell-border" style="padding:4px; font-size:10px;">Date of issue:</th>
            <th class="cell-border" style="padding:4px; font-size:10px;">Date of expire:</th>
        </tr>
        </thead>
        <tbody style="font-size:10px;">
        @foreach($competencyFixed as $i => $c)
        <tr>
            <td class="cell-border td-label" style="padding:4px;">{{ $competencyLabels[$i] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $c['number'] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $c['placeOfIssue'] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $c['dateOfIssue'] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $c['dateOfExpire'] }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="5" class="cell-border td-label" style="text-align:center; padding:4px;">If you have any other competency certificates, please provide the details below.</td>
        </tr>
        @foreach($competencyOther as $c)
        <tr>
            <td class="cell-border" style="padding:8px;">{{ $c['customLabel'] }}</td>
            <td class="cell-border" style="padding:8px;">{{ $c['number'] }}</td>
            <td class="cell-border" style="padding:8px;">{{ $c['placeOfIssue'] }}</td>
            <td class="cell-border" style="padding:8px;">{{ $c['dateOfIssue'] }}</td>
            <td class="cell-border" style="padding:8px;">{{ $c['dateOfExpire'] }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>

    {{-- Other certificates --}}
    <table class="cell-border mb-4" style="width:100%;">
        <thead>
        <tr>
            <th colspan="5" class="table-header cell-border" style="font-size:10px; padding:4px;">OTHER CERTIFICATES</th>
        </tr>
        <tr class="table-header-sub">
            <th class="cell-border" style="padding:4px;"></th>
            <th class="cell-border" style="padding:4px; font-size:10px;">Number:</th>
            <th class="cell-border" style="padding:4px; font-size:10px;">Place of issue:</th>
            <th class="cell-border" style="padding:4px; font-size:10px;">Date of issue:</th>
            <th class="cell-border" style="padding:4px; font-size:10px;">Date of expire:</th>
        </tr>
        </thead>
        <tbody style="font-size:10px;">
        @foreach($otherCertFixed as $i => $c)
        <tr>
            <td class="cell-border td-label" style="padding:4px;">{!! $otherCertLabels[$i] !!}</td>
            <td class="cell-border" style="padding:4px;">{{ $c['number'] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $c['placeOfIssue'] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $c['dateOfIssue'] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $c['dateOfExpire'] }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="5" class="cell-border td-label" style="text-align:center; padding:4px;">If you have any other certificates, please provide the details below.</td>
        </tr>
        @foreach($otherCertOther as $c)
        <tr>
            <td class="cell-border" style="padding:8px;">{{ $c['customLabel'] }}</td>
            <td class="cell-border" style="padding:8px;">{{ $c['number'] }}</td>
            <td class="cell-border" style="padding:8px;">{{ $c['placeOfIssue'] }}</td>
            <td class="cell-border" style="padding:8px;">{{ $c['dateOfIssue'] }}</td>
            <td class="cell-border" style="padding:8px;">{{ $c['dateOfExpire'] }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>

    {{-- Sea service --}}
    <table class="cell-border mb-4" style="width:100%;">
        <thead>
        <tr>
            <th colspan="9" class="table-header cell-border" style="font-size:10px; padding:4px;">PREVIOUS SEA SERVICE (FOR THE LAST 5 YEARS)</th>
        </tr>
        <tr class="table-header-sub">
            <th class="cell-border" style="padding:4px; font-size:9px;">Rank:</th>
            <th class="cell-border" style="padding:4px; font-size:9px;">Company:</th>
            <th class="cell-border" style="padding:4px; font-size:9px;">Flag:</th>
            <th class="cell-border" style="padding:4px; font-size:9px;">Name of<br>vessel:</th>
            <th class="cell-border" style="padding:4px; font-size:9px;">GRT/DWT:</th>
            <th class="cell-border" style="padding:4px; font-size:9px;">Vessel<br>type:</th>
            <th class="cell-border" style="padding:4px; font-size:9px;">Engine/<br>kWt:</th>
            <th class="cell-border" style="padding:4px; font-size:9px;">Date sign<br>on:</th>
            <th class="cell-border" style="padding:4px; font-size:9px;">Date sign<br>off:</th>
        </tr>
        </thead>
        <tbody style="font-size:10px;">
        @forelse($seaService as $r)
        <tr>
            <td class="cell-border" style="padding:4px;">{{ $r['rank'] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $r['company'] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $r['flag'] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $r['vesselName'] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $r['grtDwt'] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $r['vesselType'] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $r['engineKw'] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $r['signOn'] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $r['signOff'] }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="cell-border" style="padding:4px; color:#666;">—</td>
        </tr>
        @endforelse
        </tbody>
    </table>

    {{-- Education --}}
    <table class="cell-border mb-4" style="width:100%;">
        <thead>
        <tr>
            <th colspan="4" class="table-header cell-border" style="font-size:10px; padding:4px;">MARINE EDUCATION</th>
        </tr>
        <tr class="table-header-sub">
            <th class="cell-border" style="padding:4px; font-size:10px;">Name of school:</th>
            <th class="cell-border" style="padding:4px; font-size:10px;">From:</th>
            <th class="cell-border" style="padding:4px; font-size:10px;">Till:</th>
            <th class="cell-border" style="padding:4px; font-size:10px;">Type of degree:</th>
        </tr>
        </thead>
        <tbody style="font-size:10px;">
        @forelse($education as $e)
        <tr>
            <td class="cell-border" style="padding:4px;">{{ $e['schoolName'] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $e['from'] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $e['till'] }}</td>
            <td class="cell-border" style="padding:4px;">{{ $e['degreeType'] }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="cell-border" style="padding:4px; color:#666;">—</td>
        </tr>
        @endforelse
        </tbody>
    </table>

    {{-- Safety clothes --}}
    <table class="cell-border mb-4" style="width:100%;">
        <thead>
        <tr>
            <th colspan="4" class="table-header cell-border" style="font-size:10px; padding:4px;">SAFETY CLOTHES</th>
        </tr>
        </thead>
        <tbody style="font-size:10px;">
        <tr>
            <td class="cell-border td-label" style="width:25%; padding:4px;">Overall size:</td>
            <td class="cell-border" style="width:25%; padding:4px;">{{ $safetyOverallSize }}</td>
            <td class="cell-border td-label" style="width:25%; padding:4px;">Height:</td>
            <td class="cell-border" style="width:25%; padding:4px;">{{ $safetyHeight }}</td>
        </tr>
        <tr>
            <td class="cell-border td-label" style="padding:4px;">Shoe size:</td>
            <td class="cell-border" style="padding:4px;">{{ $safetyShoeSize }}</td>
            <td class="cell-border td-label" style="padding:4px;">Weight:</td>
            <td class="cell-border" style="padding:4px;">{{ $safetyWeight }}</td>
        </tr>
        </tbody>
    </table>

    <p class="consent">
        Настоящим я подтверждаю, что все данные, насколько они были мне известны, точны и верны и что у меня имеются законным порядком приобретенные квалификация, рабочий диплом и сертификаты. Я также подтверждаю, что я не являюсь объектом уголовных преследований и не имею никаких финансовых задолженностей перед третьими лицами.
    </p>
    <p class="consent">
        Отправляя анкету, я предоставляю согласие ООО «Марин Техникал Солюшионс» на обработку, включая сбор, запись, систематизацию, накопление, хранение, уточнение, извлечение, использование, передачу (предоставление, доступ), обезличивание, блокирование, удаление, уничтожение, своих персональных данных в документальной и/или электронной форме в соответствии со ст. 9 Федерального закона от 27.07.2006 № 152-ФЗ «О персональных данных».
    </p>

    <p class="consent">
        I confirm that the details given are to the best of my knowledge accurate and true, that I am in legal possession of the above qualifications and certificates. I also confirm that I have no unspent criminal convictions and I have no financial debts to third parties.
    </p>
    <p class="consent mb-4">
        By submitting the application form, I give my consent to LLC "Marine Technical Solutions" for the processing of personal data, including collection, recording, systematization, accumulation, storage, clarification, extraction, use, transfer (provision, access), depersonalization, blocking, deletion, destruction, of my personal data in documentary and/or electronic form in accordance with Article 9 of Federal Law No. 152-FZ dated 07/27/2006 "On Personal Data".
    </p>

    <p class="consent" style="font-size:10px;">
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

    <div class="footer-note">
        <p style="margin:0;">
            Рекомендуемое имя файла / Suggested file name pattern: <strong>LastName_FirstName_pos_…positions…_ship_…vessel_types…_shortId.pdf</strong> (короткий уникальный суффикс из UUID добавляется автоматически / a short unique id suffix is taken from the form UUID)
        </p>
    </div>

    <table style="width:100%;">
        <tr>
            <td class="footer-note" style="vertical-align:top;">
                <p style="margin:0;">Пожалуйста, отправьте эту анкету на адрес</p>
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
