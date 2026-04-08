{{ config('app.name') }} — ответ на обращение
────────────────────────────────────────

Здравствуйте@if($feedbackMessage->name), {{ $feedbackMessage->name }}@endif

{{ $replyBody }}

────────────────────────────────────────
Ваше исходное сообщение:
{{ $feedbackMessage->message }}

────────────────────────────────────────
С уважением,
{{ $sender->name }}
{{ config('app.name') }}

@if(count($attachmentDescriptors) > 0)
К письму приложены файлы ({{ count($attachmentDescriptors) }}).
@endif
