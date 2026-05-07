{{ config('app.name') }} — заявка #{{ $pageInquiry->id }}
Страница: {{ $sourcePageLabelRu }}
────────────────────────────────────────

{{ $pageInquiry->name }} · {{ $pageInquiry->company }}
@if($pageInquiry->position)
Должность: {{ $pageInquiry->position }}
@endif
E-mail: {{ $pageInquiry->email }}
@if($pageInquiry->phone)
Телефон: {{ $pageInquiry->phone }}
@endif

Флот: {{ $pageInquiry->vessels_count }}
Флаг: {{ $pageInquiry->vessel_flag }}
@if($pageInquiry->main_ports)
Порты: {{ $pageInquiry->main_ports }}
@endif
@if(count($vesselTypesHuman ?? []) > 0)
Типы судна: {{ implode(', ', $vesselTypesHuman) }}
@endif
@if(count($requiredServicesHuman ?? []) > 0)
Услуги: {{ implode(', ', $requiredServicesHuman) }}
@endif
@if($pageInquiry->message)

Сообщение:
{{ $pageInquiry->message }}
@endif

────────────────────────────────────────
Внутреннее уведомление · {{ config('app.name') }}
