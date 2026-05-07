@extends('emails.layouts.mts')

@section('email_title', 'Заявка #'.$pageInquiry->id.' — '.config('app.name'))

@section('preheader')
    Новая заявка от {{ $pageInquiry->company }} · {{ $sourcePageLabelRu }}
@endsection

@section('heading')
    Новая заявка с сайта
@endsection

@section('footer_signature')
    <span style="font-size:12px;color:#6c757d;">Внутреннее уведомление</span>
@endsection

@section('content')
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td style="padding:28px 28px 24px 28px;">
                <p style="margin:0 0 16px 0;font-size:16px;line-height:1.6;color:#1c1c1e;">
                    Здравствуйте.
                </p>
                <div style="width:48px;height:2px;background-color:#c14041;margin-bottom:18px;"></div>
                <p style="margin:0 0 12px 0;font-size:15px;line-height:1.65;color:#6c757d;">
                    Поступила заявка <strong style="color:#212529;">#{{ $pageInquiry->id }}</strong>
                    со страницы
                    <strong style="color:#c14041;font-weight:600;">{{ $sourcePageLabelRu }}</strong>.
                </p>
                <p style="margin:0 0 8px 0;font-size:15px;line-height:1.65;color:#6c757d;">
                    <strong style="color:#1c1c1e;">{{ $pageInquiry->name }}</strong>
                    · {{ $pageInquiry->company }}
                    @if($pageInquiry->position)
                        <br><span style="font-size:14px;">Должность: {{ $pageInquiry->position }}</span>
                    @endif
                </p>
                <p style="margin:12px 0 0 0;font-size:15px;line-height:1.65;color:#6c757d;">
                    E-mail:
                    <a href="mailto:{{ $pageInquiry->email }}" style="color:#c14041;text-decoration:none;">{{ $pageInquiry->email }}</a>
                </p>
                @if($pageInquiry->phone)
                    @php
                        $phoneRaw = trim((string) $pageInquiry->phone);
                        $telHref = preg_replace('/[^0-9+]/u', '', $phoneRaw);
                        $telHref = $telHref !== '' ? $telHref : $phoneRaw;
                    @endphp
                    <p style="margin:6px 0 0 0;font-size:15px;line-height:1.65;color:#6c757d;">
                        Телефон:
                        <a href="tel:{{ $telHref }}" style="color:#c14041;text-decoration:none;">{{ $pageInquiry->phone }}</a>
                    </p>
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding:0 28px 28px 28px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f8f9fa;border:1px solid #e9ecef;">
                    <tr>
                        <td style="padding:14px 18px;">
                            <p style="margin:0;font-family:Consolas,'Courier New',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:#adb5bd;">Судно и услуги</p>
                            <p style="margin:8px 0 0 0;font-size:13px;line-height:1.55;color:#6c757d;">
                                Флот: <strong style="color:#1c1c1e;">{{ $pageInquiry->vessels_count }}</strong>
                            </p>
                            @php
                                $vesselFlagCode = strtoupper(trim((string) $pageInquiry->vessel_flag));
                                $vesselFlagImg = strlen($vesselFlagCode) === 2 && ctype_alpha($vesselFlagCode)
                                    ? 'https://flagcdn.com/24x18/'.strtolower($vesselFlagCode).'.png'
                                    : null;
                            @endphp
                            <p style="margin:6px 0 0 0;font-size:13px;line-height:1.55;color:#6c757d;">
                                Флаг:
                                @if($vesselFlagImg)
                                    <img
                                        src="{{ $vesselFlagImg }}"
                                        alt="Флаг {{ $vesselFlagCode }}"
                                        width="24"
                                        height="18"
                                        style="display:inline-block;vertical-align:middle;margin:0 8px 0 4px;border:1px solid #dee2e6;border-radius:2px;"
                                    />
                                @endif
                                <strong style="color:#1c1c1e;">{{ $pageInquiry->vessel_flag }}</strong>
                            </p>
                            @if($pageInquiry->main_ports)
                                <p style="margin:6px 0 0 0;font-size:13px;line-height:1.55;color:#6c757d;">
                                    Порты: {{ $pageInquiry->main_ports }}
                                </p>
                            @endif
                            @if(count($vesselTypesHuman ?? []) > 0)
                                <p style="margin:10px 0 0 0;font-size:12px;color:#495057;">
                                    Типы судна: {{ implode(', ', $vesselTypesHuman) }}
                                </p>
                            @endif
                            @if(count($requiredServicesHuman ?? []) > 0)
                                <p style="margin:6px 0 0 0;font-size:12px;color:#495057;">
                                    Услуги: {{ implode(', ', $requiredServicesHuman) }}
                                </p>
                            @endif
                            @if($pageInquiry->message)
                                <p style="margin:12px 0 0 0;font-size:13px;line-height:1.55;color:#495057;border-top:1px solid #dee2e6;padding-top:10px;">
                                    {{ $pageInquiry->message }}
                                </p>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection
