<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
</head>
<body>
    <p>Здравствуйте.</p>
    <p>
        Поступила новая анкета кандидата <strong>{{ $applicationForm->full_name }}</strong>
        (ID {{ $applicationForm->id }}).
    </p>
    <p>Копия данных во вложении в формате PDF.</p>
    <p>— {{ config('app.name') }}</p>
</body>
</html>
