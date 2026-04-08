@extends('emails.layouts.mts')

@section('email_title', 'Новая анкета #'.$applicationForm->id.' — '.config('app.name'))

@section('heading')
    Новая анкета кандидата
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
                    Поступила новая анкета кандидата <strong style="color:#212529;">{{ $applicationForm->full_name }}</strong>
                    <span style="font-family:Consolas,'Courier New',monospace;color:#c14041;font-weight:600;">(ID {{ $applicationForm->id }})</span>.
                </p>
                <p style="margin:0;font-size:15px;line-height:1.65;color:#6c757d;">
                    Копия данных во вложении в формате <strong style="color:#1c1c1e;">PDF</strong>.
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding:0 28px 28px 28px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f8f9fa;border:1px solid #e9ecef;">
                    <tr>
                        <td style="padding:14px 18px;">
                            <p style="margin:0;font-family:Consolas,'Courier New',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:#adb5bd;">Вложение</p>
                            <p style="margin:6px 0 0 0;font-size:13px;color:#6c757d;">Файл <strong style="color:#1c1c1e;">anketa-{{ $applicationForm->id }}.pdf</strong> с данными анкеты.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection
