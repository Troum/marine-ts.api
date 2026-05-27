@extends('emails.layouts.mts')

@section('email_title', 'Дозагружены документы #'.$applicationForm->id.' — '.config('app.name'))

@section('preheader')
    Кандидат {{ $applicationForm->full_name }} дозагрузил документы по анкете №{{ $applicationForm->id }}
@endsection

@section('heading')
    Дозагружены документы
@endsection

@section('footer_signature')
    <span style="font-size:12px;color:#6c757d;">Внутреннее уведомление</span>
@endsection

@section('content')
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td style="padding:28px 28px 8px 28px;">
                <p style="margin:0 0 16px 0;font-size:16px;line-height:1.6;color:#1c1c1e;">
                    Здравствуйте.
                </p>
                <div style="width:48px;height:2px;background-color:#c14041;margin-bottom:18px;"></div>
                <p style="margin:0 0 12px 0;font-size:15px;line-height:1.65;color:#6c757d;">
                    Кандидат <strong style="color:#212529;">{{ $applicationForm->full_name }}</strong>
                    <span style="font-family:Consolas,'Courier New',monospace;color:#c14041;font-weight:600;">(анкета №{{ $applicationForm->id }})</span>
                    дозагрузил документы по запросу.
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding:0 28px 20px 28px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#ffffff;border:1px solid #dee2e6;">
                    @foreach($uploadedDocuments as $document)
                        <tr>
                            <td style="padding:14px 18px;border-bottom:{{ $loop->last ? 'none' : '1px solid #e9ecef' }};">
                                <p style="margin:0 0 4px 0;font-size:14px;line-height:1.5;color:#1c1c1e;font-weight:600;">
                                    {{ $document['label'] }}
                                </p>
                                <p style="margin:0;font-size:13px;line-height:1.45;color:#6c757d;">
                                    Файл: <span style="color:#212529;">{{ $document['fileName'] }}</span>
                                </p>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding:0 28px 28px 28px;text-align:center;">
                <p style="margin:0 0 18px 0;font-size:14px;line-height:1.55;color:#6c757d;">
                    Откройте анкету в админке, чтобы просмотреть данные и скачать файлы.
                </p>
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin:0 auto;">
                    <tr>
                        <td style="background-color:#c14041;">
                            <a href="{{ $adminUrl }}" target="_blank" rel="noopener noreferrer" style="display:inline-block;padding:14px 32px;font-family:Consolas,'Courier New',monospace;font-size:11px;font-weight:600;color:#ffffff;text-decoration:none;letter-spacing:0.12em;text-transform:uppercase;">
                                Перейти в админку
                            </a>
                        </td>
                    </tr>
                </table>
                <p style="margin:20px 0 0 0;font-size:12px;line-height:1.5;color:#adb5bd;word-break:break-all;">
                    Если кнопка не работает, скопируйте ссылку в браузер:<br>
                    <a href="{{ $adminUrl }}" style="color:#c14041;text-decoration:underline;">{{ $adminUrl }}</a>
                </p>
            </td>
        </tr>
    </table>
@endsection
