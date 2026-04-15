<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Вход в Telescope — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/telescope-auth.css') }}">
</head>
<body>
    <div class="card">
        <h1>Вход в Telescope</h1>
        <p class="sub">Те же логин и пароль, что для API админки. Доступ только для адресов из TELESCOPE_DASHBOARD_EMAILS.</p>

        @if ($errors->any())
            <div class="error">
                <ul>
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ route('telescope-auth.login.store') }}">
            @csrf
            <label for="username">Логин</label>
            <input id="username" name="username" type="text" value="{{ old('username') }}" required autocomplete="username" autofocus>

            <label for="password">Пароль</label>
            <input id="password" name="password" type="password" required autocomplete="current-password">

            <div class="row">
                <input id="remember" name="remember" type="checkbox" value="1" @checked(old('remember'))>
                <label for="remember" class="remember-label">Запомнить меня</label>
            </div>

            <button type="submit">Войти</button>
        </form>
    </div>
</body>
</html>
