@extends('layouts.home')

@section('title', 'Home Page')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
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
            <div>
                <p class="title">Информация об испытании</p>
                <hr>
            </div>

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
                    <select class="js-select2" name="contest_level" сlass="js-select2"></select>
                </div>
            </div>

            <div class="form_field">
                <div class="">
                    Площадка:
                </div>
                <div class="">
                    <select class="js-select2" name="contest_place"></select>
                </div>
            </div>

            <div style="margin-top: 25px;">
                <p class="title">Информация об участнике</p>
                <hr>
            </div>


            <div class="form_field school_manual">
                <div class="">
                    Название школы:
                </div>
                <div class="">
                    <input name="school_name" placeholder="Обязательное поле">
                </div>
            </div>

            <div class="form_field region">
                <div class="">
                    Регион:
                </div>
                <div>
                    <select class="js-select2" name="region" placeholder="Выберите регион">
                        <option value=""></option>
                        <option value="01">Республика Адыгея (Адыгея)</option>
                        <option value="02">Республика Башкортостан</option>
                        <option value="03">Республика Бурятия</option>
                        <option value="04">Республика Алтай</option>
                        <option value="05">Республика Дагестан</option>
                        <option value="06">Республика Ингушетия</option>
                        <option value="07">Кабардино-Балкарская Республика</option>
                        <option value="08">Республика Калмыкия</option>
                        <option value="09">Карачаево-Черкесская Республика</option>
                        <option value="10">Республика Карелия</option>
                        <option value="11">Республика Коми</option>
                        <option value="12">Республика Марий Эл</option>
                        <option value="13">Республика Мордовия</option>
                        <option value="14">Республика Саха (Якутия)</option>
                        <option value="15">Республика Северная Осетия - Алания</option>
                        <option value="16">Республика Татарстан (Татарстан)</option>
                        <option value="17">Республика Тыва</option>
                        <option value="18">Удмуртская Республика</option>
                        <option value="19">Республика Хакасия</option>
                        <option value="20">Чеченская Республика</option>
                        <option value="21">Чувашская Республика - Чувашия</option>
                        <option value="22">Алтайский край</option>
                        <option value="23">Краснодарский край</option>
                        <option value="24">Красноярский край</option>
                        <option value="25">Приморский край</option>
                        <option value="26">Ставропольский край</option>
                        <option value="27">Хабаровский край</option>
                        <option value="28">Амурская область</option>
                        <option value="29">Архангельская область</option>
                        <option value="30">Астраханская область</option>
                        <option value="31">Белгородская область</option>
                        <option value="32">Брянская область</option>
                        <option value="33">Владимирская область</option>
                        <option value="34">Волгоградская область</option>
                        <option value="35">Вологодская область</option>
                        <option value="36">Воронежская область</option>
                        <option value="37">Ивановская область</option>
                        <option value="38">Иркутская область</option>
                        <option value="39">Калининградская область</option>
                        <option value="40">Калужская область</option>
                        <option value="41">Камчатский край</option>
                        <option value="42">Кемеровская область</option>
                        <option value="43">Кировская область</option>
                        <option value="44">Костромская область</option>
                        <option value="45">Курганская область</option>
                        <option value="46">Курская область</option>
                        <option value="47">Ленинградская область</option>
                        <option value="48">Липецкая область</option>
                        <option value="49">Магаданская область</option>
                        <option value="50">Московская область</option>
                        <option value="51">Мурманская область</option>
                        <option value="52">Нижегородская область</option>
                        <option value="53">Новгородская область</option>
                        <option value="54">Новосибирская область</option>
                        <option value="55">Омская область</option>
                        <option value="56">Оренбургская область</option>
                        <option value="57">Орловская область</option>
                        <option value="58">Пензенская область</option>
                        <option value="59">Пермский край</option>
                        <option value="60">Псковская область</option>
                        <option value="61">Ростовская область</option>
                        <option value="62">Рязанская область</option>
                        <option value="63">Самарская область</option>
                        <option value="64">Саратовская область</option>
                        <option value="65">Сахалинская область</option>
                        <option value="66">Свердловская область</option>
                        <option value="67">Смоленская область</option>
                        <option value="68">Тамбовская область</option>
                        <option value="69">Тверская область</option>
                        <option value="70">Томская область</option>
                        <option value="71">Тульская область</option>
                        <option value="72">Тюменская область</option>
                        <option value="73">Ульяновская область</option>
                        <option value="74">Челябинская область</option>
                        <option value="75">Забайкальский край</option>
                        <option value="76">Ярославская область</option>
                        <option value="77">г. Москва</option>
                        <option value="78">Санкт-Петербург</option>
                        <option value="79">Еврейская автономная область</option>
                        <option value="83">Ненецкий автономный округ</option>
                        <option value="86">Ханты-Мансийский автономный округ - Югра</option>
                        <option value="87">Чукотский автономный округ</option>
                        <option value="89">Ямало-Ненецкий автономный округ</option>
                    </select>
                </div>
            </div>

            <div class="form_field place">
                <div class="">
                    Местность:
                </div>
                <div>
                    <select class="js-select2" name="city"></select>
                </div>
            </div>

            <div class="form_field school">
                <div class="">
                    Школа:
                </div>
                <div>
                    <select class="js-select2" name="school"></select>
                </div>
            </div>

            <div class="form_field checkbox">
                <label>
                  <input type="checkbox" name="school_absend">
                  <span class="custom-checkbox"></span>
                </label>
                Моей школы нет в списке
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
            <a href="contest/{{$particip->contest_id}}/member">
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
            <a href="contest/{{$particip->contest_id}}/check">
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
<script type="text/javascript" src="{{ asset('js/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/ru.min.js') }}"></script>
@endsection
