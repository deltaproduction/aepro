@extends('layouts.member')

@section('title', 'Home Page')

@section('container')
<small class="path">
    <span><a href="/">Главная</a> / Мои участия / </span>{{$title}}
</small>
<hr><br>

<h1>{{$title}}</h1>
<small>Название испытания</small><br><br>
<h1>{{$contest_code}}</h1>
<small>Код испытания</small>
<h1>{{$reg_number}}</h1>
<small>Регистрационный номер</small>
<p>Уровень: {{$level}}</p>
<p>Площадка: {{$place}}</p>
<p>Местность: {{$locality}}</p>
@if ($address)
<p>Адрес: {{$address}}</p>
@endif

<a href="notification">Скачать уведомление</a>

@endsection

@section('scripts')
<script type="module" src="{{ asset('js/member/index.js') }}"></script>
@endsection
