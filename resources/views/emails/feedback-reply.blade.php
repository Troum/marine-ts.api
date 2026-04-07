<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ответ на обращение</title>
</head>
<body style="margin:0;padding:0;background-color:#f0f4f8;font-family:'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f0f4f8;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;margin:0 auto;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 100%);background-color:#0f172a;border-radius:12px 12px 0 0;padding:28px 32px;text-align:center;">
                            <p style="margin:0 0 8px 0;font-size:11px;letter-spacing:0.2em;text-transform:uppercase;color:#94a3b8;font-weight:600;">
                                {{ config('app.name') }}
                            </p>
                            <h1 style="margin:0;font-size:22px;line-height:1.35;font-weight:600;color:#ffffff;">
                                Ответ на ваше обращение
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#ffffff;padding:32px 28px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
                            <p style="margin:0 0 16px 0;font-size:16px;line-height:1.6;color:#1e293b;">
                                Здравствуйте@if($feedbackMessage->name), <strong style="color:#0f172a;">{{ $feedbackMessage->name }}</strong>@endif
                            </p>
                            <div style="font-size:15px;line-height:1.65;color:#334155;">
                                {!! nl2br(e($replyBody)) !!}
                            </div>
                            @if(count($attachmentDescriptors) > 0)
                                <p style="margin:24px 0 0 0;font-size:13px;color:#64748b;">
                                    К письму приложено файл(ов): <strong>{{ count($attachmentDescriptors) }}</strong>
                                </p>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#f8fafc;padding:20px 28px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
                            <p style="margin:0 0 8px 0;font-size:12px;text-transform:uppercase;letter-spacing:0.08em;color:#94a3b8;">Ваше исходное сообщение</p>
                            <p style="margin:0;font-size:13px;line-height:1.55;color:#64748b;white-space:pre-wrap;">{{ \Illuminate\Support\Str::limit($feedbackMessage->message, 2000) }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#f1f5f9;border-radius:0 0 12px 12px;border:1px solid #e2e8f0;border-top:none;padding:20px 28px;text-align:center;">
                            <p style="margin:0 0 6px 0;font-size:12px;color:#64748b;line-height:1.5;">
                                С уважением,<br>
                                <strong style="color:#475569;">{{ $sender->name }}</strong>
                            </p>
                            <p style="margin:0;font-size:11px;color:#94a3b8;">
                                {{ config('app.name') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
