@extends('layouts.creator')

@section('title', 'Home Page')

@section('mw-title', 'Новая аудитория')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/creator/auditorium.css') }}">
@endsection

@section('container')
<small class="path">
    <span><a href="/">Главная</a> / Мои испытания / <a href="/contest/{{$contest_id}}">{{$contest_title}}</a> / Площадки /  <a href="/contest/{{$contest_id}}/place/{{$place_id}}">{{$place_title}}</a> /</span> {{$title}}
</small>
<hr><br>


<div class="header">
    <div>
        <h1>{{$title}}</h1>
        <small>Название аудитории</small><br><br>
    </div>
    <div class="info">
        <div class="side">
            <div class="buttons">
                @if ($showProtocolDownloadButton)
                <a href="{{$auditorium_id}}/protocol">
                    <div class="download-button">
                        <div class="download-button__wrapper">
                            <div class="download-button__image"></div>
                            <div class="download-button__text">
                                <span>Протокол</span>
                            </div>
                        </div>
                    </div>
                </a>
                @endif

                @if ($showPapersDownloadButton)
                <a href="{{$auditorium_id}}/papers">
                    <div class="download-button">
                        <div class="download-button__wrapper">
                            <div class="download-button__image"></div>
                            <div class="download-button__text">
                                <span>Работы</span>
                            </div>
                        </div>
                    </div>
                </a>
                @endif

                <!-- <a href="notification">Скачать уведомление
                <a href="notification">Скачать уведомление</a> -->
            </div>
        </div>
    </div>
</div>

<hr><br>

<h1>Состав аудитории</h1>
<hr>

<table class="contest_members_table">
	<thead>
		<tr>
			<th style="width: 5%">№</th>
			<th style="width: 55%">ФИО</th>
            <th style="width: 32%">E-mail</th>
            <th style="width: 3%">Место</th>
            <th style="width: 5%"></th>
		</tr>
	</thead>
    <tbody>
        @foreach ($members as $index => $member)
    		<tr>
    			<td>{{$index + 1}}</td>
    			<td>{{$member->last_name}} {{$member->first_name}} {{$member->middle_name}}</td>
                <td>{{$member->email}}</td>
                <td>{{$member->seat}}</td>
                <td>X</td>
    		</tr>
        @endforeach
	</tbody>
</table>

@endsection

@section('scripts')
@endsection
