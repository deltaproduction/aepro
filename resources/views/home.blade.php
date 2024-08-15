@extends('layouts.home')

@section('title', 'Home Page')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/home/main.css') }}">
@endsection

@section('mw-title', '')

@section('mw-content')
<div class="mw-newContest">
    <form id="newContest_form">
        <div class="form_wrapper">
            <div class="form_field-big">
                <div class="">
                    Название испытания:
                </div>
                <div class="">
                    <input type="text" name="contest_title" placeholder="Обязательное поле" autocomplete="off">
                </div>
            </div>

            <div class="form_submit-field">
                <button type="submit" class="submit-button">Добавить</button>
            </div>
        </div>
    </form>
</div>
<div class="mw-newParticip">
    <form id="newParticip_form">
        <input type="hidden" name="code">
        <div class="form_wrapper">
            <div class="form_field">
                <div class="">
                    Код испытания:
                </div>
                <div class="">
                    <span class="contest-code"></span>
                </div>
            </div>

            <div class="form_field">
                <div class="">
                    Уровень:
                </div>
                <div class="">
                    <select name="contest_level"></select>
                </div>
            </div>

            <div class="form_field">
                <div class="">
                    Площадка:
                </div>
                <div class="">
                    <select name="contest_place"></select>
                </div>
            </div>

            <div class="form_submit-field">
                <button type="submit" class="submit-button">Завершить</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('container')

<div class="new-particip_form__back">
    <div class="new-particip_form__block">
        <div class="new-particip_form__block__wrapper">
            <h1>Регистрация в испытании</h1>
            <form id="getContest_form">
                <div class="form_wrapper">
                    <div class="form_field-big">
                        <div class="">
                            Код испытания:
                        </div>
                        <div class="">
                            <input type="text" name="contest_code" placeholder="Обязательное поле" autocomplete="off">
                        </div>
                    </div>

                    <div class="form_submit-field">
                        <button type="submit" class="submit-button">Далее</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@if (!$particips->isEmpty())
    <h1>Мои участия</h1>
    <hr>

    <div class="cards_block">
        @foreach ($particips as $particip)
            <a href="contest/{{$particip->contest_id}}/check">
                <div class="card">
                    <div class="card_wrapper">
                        <h1>{{$particip->ct}}</h1>
                        <br>
                        <div>
                            <p><b>Уровень:</b> {{$particip->lt}}</p>
                            <p><b>Площадка:</b> {{$particip->pt}}</p>
                        </div>

                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <br><br>
@endif

@if (!$contests->isEmpty())
    <h1>Мои испытания</h1>
    <hr>

    <div class="cards_block">
        @foreach ($contests as $contest)
            <a href="contest/{{$contest->id}}">
                <div class="card">
                    <div class="card_wrapper">
                        <h1>{{$contest->title}}</h1>
                        <br>
                        <div>
                            <p><small>Код испытания:</small></p>
                            <span class="contest-code">{{$contest->contest_code}}</span>
                        </div>

                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <br><br>
@endif

@if (!$experts->isEmpty())
    <h1>Мои проверки</h1>
    <hr>

    <div class="cards_block">
        @foreach ($experts as $expert)
            <a href="contest/">
                <div class="card">
                    <div class="card_wrapper">
                        <h1>{{$expert['title']}}</h1>
                        <br>
                        <div>
                            <p style="line-height: 2em;">
                                <small>Уровни:</small><br>
                                @foreach ($expert['levels'] as $level)
                                    <span class="level">{{ $level['title'] }}</span>
                                @endforeach
                            </p>
                        </div>

                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <br><br>
@endif

@endsection


@section('scripts')
<script type="module" src="{{ asset('js/home/index.js') }}"></script>
@endsection
