<!DOCTYPE html>
<html lang="ru" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>Запрос документов</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#f0f4f8;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;font-family:'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;">
        Загрузите документы по ссылке из письма — анкета №{{ $applicationForm->id }}
    </div>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f0f4f8;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;margin:0 auto;">
                    {{-- Шапка --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 100%);background-color:#0f172a;border-radius:12px 12px 0 0;padding:28px 32px;text-align:center;">
                            <p style="margin:0 0 8px 0;font-size:11px;letter-spacing:0.2em;text-transform:uppercase;color:#94a3b8;font-weight:600;">
                                {{ config('app.name') }}
                            </p>
                            <h1 style="margin:0;font-size:22px;line-height:1.35;font-weight:600;color:#ffffff;letter-spacing:-0.02em;">
                                Запрос дополнительных документов
                            </h1>
                        </td>
                    </tr>
                    {{-- Тело --}}
                    <tr>
                        <td style="background-color:#ffffff;padding:0 1px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="padding:32px 28px 8px 28px;">
                                        <p style="margin:0 0 16px 0;font-size:16px;line-height:1.6;color:#1e293b;">
                                            Здравствуйте, <strong style="color:#0f172a;">{{ $applicationForm->full_name }}</strong>
                                        </p>
                                        <p style="margin:0 0 8px 0;font-size:15px;line-height:1.65;color:#475569;">
                                            По вашей анкете <span style="color:#0f172a;font-weight:600;">№{{ $applicationForm->id }}</span> необходимо предоставить следующие документы:
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 28px 24px 28px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;overflow:hidden;">
                                            @foreach($labels as $label)
                                                <tr>
                                                    <td style="padding:14px 18px;border-bottom:{{ $loop->last ? 'none' : '1px solid #e2e8f0' }};">
                                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                                            <tr>
                                                                <td width="28" valign="top" style="padding-top:2px;">
                                                                    <span style="display:inline-block;width:22px;height:22px;line-height:22px;text-align:center;background-color:#e0f2fe;color:#0369a1;border-radius:6px;font-size:12px;font-weight:700;">{{ $loop->iteration }}</span>
                                                                </td>
                                                                <td style="font-size:14px;line-height:1.5;color:#334155;padding-left:4px;">
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
                                        <p style="margin:0 0 18px 0;font-size:14px;line-height:1.55;color:#64748b;">
                                            Нажмите кнопку ниже, чтобы перейти к безопасной загрузке файлов.<br>
                                            <span style="font-size:13px;color:#94a3b8;">Ссылка действует ограниченное время.</span>
                                        </p>
                                        {{-- Кнопка (bulletproof) --}}
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin:0 auto;">
                                            <tr>
                                                <td style="border-radius:10px;background:linear-gradient(180deg,#0ea5e9 0%,#0284c7 100%);background-color:#0284c7;">
                                                    <a href="{{ $uploadUrl }}" target="_blank" rel="noopener noreferrer" style="display:inline-block;padding:16px 36px;font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;border-radius:10px;letter-spacing:0.02em;">
                                                        Перейти к загрузке документов
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                        <p style="margin:20px 0 0 0;font-size:12px;line-height:1.5;color:#94a3b8;word-break:break-all;">
                                            Если кнопка не работает, скопируйте ссылку в браузер:<br>
                                            <a href="{{ $uploadUrl }}" style="color:#0284c7;text-decoration:underline;">{{ $uploadUrl }}</a>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 28px 28px 28px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#fffbeb;border:1px solid #fde68a;border-radius:8px;">
                                            <tr>
                                                <td style="padding:14px 18px;">
                                                    <p style="margin:0;font-size:13px;line-height:1.55;color:#92400e;">
                                                        <strong style="color:#b45309;">Важно:</strong> не пересылайте это письмо и ссылку третьим лицам — доступ привязан к вашей анкете.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    {{-- Подвал --}}
                    <tr>
                        <td style="background-color:#f1f5f9;border-radius:0 0 12px 12px;border:1px solid #e2e8f0;border-top:none;padding:20px 28px;text-align:center;">
                            <p style="margin:0 0 6px 0;font-size:12px;color:#64748b;line-height:1.5;">
                                С уважением,<br>
                                <strong style="color:#475569;">{{ config('app.name') }}</strong>
                            </p>
                            <p style="margin:0;font-size:11px;color:#94a3b8;">
                                Это автоматическое сообщение, отвечать на него не нужно.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
