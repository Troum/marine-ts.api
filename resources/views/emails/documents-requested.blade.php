@extends('emails.layouts.mts')

@section('email_title', 'Запрос документов — '.config('app.name'))

@section('preheader')
    Загрузите документы по ссылке из письма — анкета №{{ $applicationForm->id }}
@endsection

@section('heading')
    Запрос дополнительных документов
@endsection

@section('footer_signature')
    С уважением,<br>
    <strong style="color:#1c1c1e;">{{ config('app.name') }}</strong>
@endsection

@section('footer_extra')
    <p style="margin:12px 0 0 0;font-size:11px;line-height:1.5;color:#adb5bd;">
        Это автоматическое сообщение, отвечать на него не нужно.
    </p>
@endsection

@section('content')
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td style="padding:28px 28px 8px 28px;">
                <p style="margin:0 0 16px 0;font-size:16px;line-height:1.6;color:#1c1c1e;">
                    Здравствуйте, <strong style="color:#212529;">{{ $applicationForm->full_name }}</strong>
                </p>
                <p style="margin:0 0 8px 0;font-size:15px;line-height:1.65;color:#6c757d;">
                    По вашей анкете <span style="color:#c14041;font-weight:600;font-family:Consolas,'Courier New',monospace;">№{{ $applicationForm->id }}</span> необходимо предоставить следующие документы:
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding:0 28px 20px 28px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#ffffff;border:1px solid #dee2e6;">
                    @foreach($labels as $label)
                        <tr>
                            <td style="padding:14px 18px;border-bottom:{{ $loop->last ? 'none' : '1px solid #e9ecef' }};">
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td width="36" valign="top" style="padding-top:1px;">
                                            <span style="display:inline-block;min-width:24px;height:24px;line-height:24px;text-align:center;background-color:#c14041;color:#ffffff;font-size:12px;font-weight:700;font-family:Consolas,'Courier New',monospace;">{{ $loop->iteration }}</span>
                                        </td>
                                        <td style="font-size:14px;line-height:1.5;color:#1c1c1e;padding-left:6px;">
                                            {{ $label }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding:0 28px 28px 28px;text-align:center;">
                <p style="margin:0 0 18px 0;font-size:14px;line-height:1.55;color:#6c757d;">
                    Нажмите кнопку ниже, чтобы перейти к безопасной загрузке файлов.<br>
                    <span style="font-size:13px;color:#adb5bd;">Ссылка действует ограниченное время.</span>
                </p>
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin:0 auto;">
                    <tr>
                        <td style="background-color:#c14041;">
                            <a href="{{ $uploadUrl }}" target="_blank" rel="noopener noreferrer" style="display:inline-block;padding:14px 32px;font-family:Consolas,'Courier New',monospace;font-size:11px;font-weight:600;color:#ffffff;text-decoration:none;letter-spacing:0.12em;text-transform:uppercase;">
                                Перейти к загрузке документов
                            </a>
                        </td>
                    </tr>
                </table>
                <p style="margin:20px 0 0 0;font-size:12px;line-height:1.5;color:#adb5bd;word-break:break-all;">
                    Если кнопка не работает, скопируйте ссылку в браузер:<br>
                    <a href="{{ $uploadUrl }}" style="color:#c14041;text-decoration:underline;">{{ $uploadUrl }}</a>
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding:0 28px 28px 28px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#fff5f5;border:1px solid #f5c2c7;border-left:3px solid #c14041;">
                    <tr>
                        <td style="padding:14px 18px;">
                            <p style="margin:0;font-size:13px;line-height:1.55;color:#6c757d;">
                                <strong style="color:#c14041;">Важно:</strong> не пересылайте это письмо и ссылку третьим лицам — доступ привязан к вашей анкете.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection
