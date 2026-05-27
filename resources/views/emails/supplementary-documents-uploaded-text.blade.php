{{ config('app.name') }} — дозагружены документы #{{ $applicationForm->id }}
────────────────────────────────────────

Здравствуйте.

Кандидат {{ $applicationForm->full_name }} (анкета №{{ $applicationForm->id }}) дозагрузил документы по запросу.

Загруженные файлы:
@foreach($uploadedDocuments as $document)
- {{ $document['label'] }} — {{ $document['fileName'] }}
@endforeach

Открыть анкету в админке:
{{ $adminUrl }}

────────────────────────────────────────
Внутреннее уведомление · {{ config('app.name') }}
