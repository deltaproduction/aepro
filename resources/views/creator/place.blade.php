@extends('layouts.creator')

@section('title', 'Home Page')

@section('mw-title', 'Новая аудитория')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/creator/place.css') }}">
@endsection

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

            <div class="form_field">
                <div class="">
                    Уровень:
                </div>
                <div class="">
                    <select name="level">
                        @foreach ($levels as $level)
                            <option value="{{$level->id}}">{{$level->title}}</option>
                        @endforeach
                    </select>
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
    <div class="info">
        <div class="side">
            <div class="buttons">
                <a href="{{$place_id}}/protocols">
                    <div class="download-button">
                        <div class="download-button__wrapper">
                            <div class="download-button__image"></div>
                            <div class="download-button__text">
                                <span>Протоколы</span>
                            </div>
                        </div>
                    </div>
                </a>

<!--                 <a href="{{$place_id}}/lni">
                    <div class="download-button">
                        <div class="download-button__wrapper">
                            <div class="download-button__image"></div>
                            <div class="download-button__text">
                                <span>ЛНИ</span>
                            </div>
                        </div>
                    </div>
                </a> -->

                <a href="{{$place_id}}/papers">
                    <div class="download-button">
                        <div class="download-button__wrapper">
                            <div class="download-button__image"></div>
                            <div class="download-button__text">
                                <span>Работы</span>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- <a href="notification">Скачать уведомление
                <a href="notification">Скачать уведомление</a> -->
                @if ($at == 4)
                    <a href="{{$place_id}}/ppi_file">
                        <div class="download-button">
                            <div class="download-button__wrapper">
                                <div class="download-button__image"></div>
                                <div class="download-button__text">
                                    <span>Файл ППИ</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endif

            </div>
        </div>
    </div>
</div>


<hr><br>

<h1>Информация о площадке</h1>
<hr>

<pre><b>Местность:</b>		<span>{{$locality}}</span></pre>
@if ($address)
<pre><b>Адрес:</b>			<span>{{$address}}</span></pre>
@endif
<pre><b>Мест:</b>			<span>{{$places_count}}</span></pre>
<br><br>

<h1>Аудитории</h1>
<hr>
<div class="cards_block">
    @foreach ($auditoriums as $auditorium)
    <a href="{{$place_id}}/auditorium/{{$auditorium->id}}">
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

<br><br>

<h1>Участники</h1>
<hr>

<table class="experts_table">
	<thead>
		<tr>
			<th style="width: 5%">№</th>
			<th style="width: 55%">ФИО</th>
            <th style="width: 35%">E-mail</th>
            <th style="width: 5%"></th>
		</tr>
	</thead>
	<tbody>
        @foreach ($members as $index => $member)
    		<tr>
    			<td>{{$index + 1}}</td>
    			<td>{{$member->last_name}} {{$member->first_name}} {{$member->middle_name}}</td>
                <td>{{$member->email}}</td>
                <td>X</td>
    		</tr>
        @endforeach
	</tbody>
</table>

@endsection

@section('scripts')
<script type="module" src="{{ asset('js/creator/contest/place/index.js') }}"></script>
@endsection
