<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
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
                <h1>Создание аккаунта</h1>
            </div>

            <div class="auth-card__form">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form_wrapper">
                        <div class="form_field">
                            <div class="">
                                Фамилия:
                            </div>
                            <div class="">
                                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required  autocomplete="off">
                            </div>
                        </div>

                        <div class="form_field">
                            <div class="">
                                Имя:
                            </div>
                            <div class="">
                                <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required  autocomplete="off">
                            </div>
                        </div>

                        <div class="form_field">
                            <div class="">
                                Отчество:
                            </div>
                            <div class="">
                                <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}" required  autocomplete="off">
                            </div>
                        </div>

                        <div class="form_field">
                            <div class="">
                                E-mail:
                            </div>
                            <div class="">
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required  autocomplete="off">
                            </div>
                        </div>

                        <div class="form_field">
                            <div class="">
                                Пароль:
                            </div>
                            <div class="">
                                <input type="password" id="password" name="password" required  autocomplete="off">
                            </div>
                        </div>

                        <div class="form_field checkbox">
                            <label>
                              <input type="checkbox" name="pd_accept">
                              <span class="custom-checkbox"></span>
                            </label>
                            Согласен на обработку персональных данных
                        </div>

                        <div class="form_submit-field">
                            <a href="login">Войти в систему</a>
                            <button type="submit" class="submit-button">Создать</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
