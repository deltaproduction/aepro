@extends('layouts.creator')

@section('title', 'Home Page')

@section('container')
<small class="path">
    <span><a href="/">Главная</a> / Мои испытания / <a href="/contest/{{$contest_id}}">{{$contest_title}}</a> / Уровни / <a href="/contest/{{$contest_id}}/level/{{$level_id}}">{{$level_title}}</a> / </span>Задание {{$task_number}}
</small>
<hr><br>

<h1>Задание №{{$task_number}}</h1>
<small>Номер задания</small><br><br>
<p>Макс.балл: {{$task_max_rate}}</p>

@endsection

@section('scripts')
<script type="module" src="{{ asset('js/creator/contest/place/index.js') }}"></script>
@endsection
