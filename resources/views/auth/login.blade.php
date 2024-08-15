<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <div class="auth">
        <div class="logo"></div>
        <div class="auth-card">
            <div class="auth-card__title">
                <h1>Вход в систему</h1>
            </div>

            <div class="auth-card__form">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form_wrapper">
                        <div class="form_field">
                            <div class="">
                                E-mail:
                            </div>
                            <div class="">
                                <input type="email" name="email" autocomplete="off">
                            </div>
                        </div>
                        <div class="form_field">
                            <div class="">
                                Пароль:
                            </div>
                            <div class="">
                                <input type="password" name="password" autocomplete="off">
                            </div>
                        </div>
                        <div class="form_submit-field">
                            <a href="register">Создать аккаунт</a>
                            <button type="submit" class="submit-button">Войти</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div>
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
</body>
</html>
