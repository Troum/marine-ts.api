@extends('emails.layouts.mts')

@section('email_title', 'Ответ на обращение — '.config('app.name'))

@section('heading')
    Ответ на ваше обращение
@endsection

@section('footer_signature')
    С уважением,<br>
    <strong style="color:#1c1c1e;">{{ $sender->name }}</strong>
@endsection

@section('content')
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td style="padding:28px 28px 8px 28px;">
                <p style="margin:0 0 16px 0;font-size:16px;line-height:1.6;color:#1c1c1e;">
                    Здравствуйте@if($feedbackMessage->name), <strong style="color:#212529;">{{ $feedbackMessage->name }}</strong>@endif
                </p>
                <div style="width:48px;height:2px;background-color:#c14041;margin-bottom:18px;"></div>
                <div style="font-size:15px;line-height:1.65;color:#6c757d;">
                    {!! nl2br(e($replyBody)) !!}
                </div>
                @if(count($attachmentDescriptors) > 0)
                    <p style="margin:24px 0 0 0;font-size:13px;color:#6c757d;font-family:Consolas,'Courier New',monospace;">
                        Вложений: <strong style="color:#1c1c1e;">{{ count($attachmentDescriptors) }}</strong>
                    </p>
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding:0 28px 28px 28px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f8f9fa;border:1px solid #e9ecef;">
                    <tr>
                        <td style="padding:16px 18px;">
                            <p style="margin:0 0 8px 0;font-family:Consolas,'Courier New',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:#adb5bd;">Ваше исходное сообщение</p>
                            <p style="margin:0;font-size:13px;line-height:1.55;color:#6c757d;white-space:pre-wrap;">{{ \Illuminate\Support\Str::limit($feedbackMessage->message, 2000) }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection
