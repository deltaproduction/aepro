@extends('layouts.creator')

@section('title', 'Home Page')

@section('mw-title', 'Новая аудитория')

@section('mw-content')
<div class="mw-newAuditorium">
    <form id="newAuditorium_form">
        <input type="hidden" name="contest_id" value="{{$contest_id}}">
        <input type="hidden" name="place_id" value="{{$place_id}}">
        <div class="form_wrapper">
            <div class="form_field">
                <div class="">
                    Название:
                </div>
                <div class="">
                    <input type="text" name="auditorium_title" autocomplete="off" placeholder="Обязательное поле">
                </div>
            </div>

            <div class="form_field">
                <div class="">
                    Рядов:
                </div>
                <div class="">
                    <input type="text" name="auditorium_columns" autocomplete="off" placeholder="Обязательное поле">
                </div>
            </div>

            <div class="form_field">
                <div class="">
                    Парт в ряду:
                </div>
                <div class="">
                    <input type="text" name="auditorium_rows" autocomplete="off" placeholder="Обязательное поле">
                </div>
            </div>

            <div class="form_submit-field">
                <button type="submit" class="submit-button">Добавить</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('container')
<small class="path">
    <span><a href="/">Главная</a> / Мои испытания / <a href="/contest/{{$contest_id}}">{{$contest_title}}</a> / Площадки / </span>{{$title}}
</small>
<hr><br>

<div class="header">
    <div>
        <h1>{{$title}}</h1>
        <small>Название площадки</small><br><br>
    </div>
</div>


<hr><br>

<h1>Информация о площадке</h1>
<hr>

<pre><b>Местность:</b>		<span>{{$locality}}</span></pre>
@if ($address)
<pre><b>Адрес:</b>			<span>{{$address}}</span></pre>
@endif

<br><br>

<h1>Аудитории</h1>
<div class="cards_block">
    @foreach ($auditoriums as $auditorium)
    <a>
        <div class="card">
            <div class="card_wrapper">
                <h1>{{$auditorium->title}}</h1>
                <div>
                    <p><b>Рядов:</b> {{$auditorium->columns}} </p>
                    <p><b>Парт в ряду:</b> {{$auditorium->rows}} </p>
                    <p><b>Мест:</b>  {{$auditorium->rows * $auditorium->columns}}</p>
                </div>
            </div>
        </div>
    </a>
    @endforeach
    <div>
        <div class="card new-card new-auditorium">
            <div class="card_wrapper"></div>
        </div>
    </div>
</div>


@endsection

@section('scripts')
<script type="module" src="{{ asset('js/creator/contest/place/index.js') }}"></script>
@endsection
