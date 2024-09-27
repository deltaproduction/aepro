@extends('layouts.creator')

@section('title', 'Home Page')

@section('mw-title', 'Новая аудитория')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/creator/appeal.css') }}">
@endsection

@section('container')
    <small class="path">
        <span><a href="/">Главная</a> / Мои испытания / <a href="/contest/{{$contest_id}}">{{$contest_title}}</a> / Апелляции / </span> {{$appeal_title}}
    </small>
    <hr><br>


    <div class="header">
        <div>
            <h1>{{$appeal_title}}</h1>
            <small>Имя участника</small><br><br>
        </div>
    </div>

    <hr><br>


    <h1>Контакты для связи</h1>
    <hr>

    <pre><b>E-mail:</b>			<span>{{$email}}</span></pre>
    <pre><b>Телефон:</b>		<span>{{$phone}}</span></pre>
    <br><br>

    <h1>Текст обращения</h1>
    <hr>

    <pre style="padding: 10px; border: .5px solid #dcdcdc;">{{$text}}</pre>
    <br><br>


    <h1>Сканы работы</h1>
    <hr>

    @if ($scans)
        @foreach ($scans as $scan)
            <a href="/scan/{{$scan->path}}" target="_blank">Лист {{$scan->page_number}}</a><br>
        @endforeach
    @else
        Сканов нет.
    @endif
    <br><br><br>

    <h1>Результат</h1>
    <hr>
    <br>
    <form id="saveData" action="{{ route('saveGrades') }}" method="POST">
        @csrf
        <input type="hidden" name="contest_id" value="{{$contest_id}}">
        <input type="hidden" name="contest_member_id" value="{{$c_member->id}}">
        <table>
            <thead>
            <tr>
                <th>№</th>
                @foreach($tasks as $task)
                    <th style="text-align: center;">{{$task->number}}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Балл</td>
                @foreach($tasks as $task)
                    <td style="text-align: center;">
                        @if ($c_member->grades()->count())
                            <input type="number" min="0" max="{{$task->max_rate}}" name="task_{{$task->id}}" class="rate_input" value="{{$c_member->grades()->where("task_id", $task->id)->first()->final_score}}">
                        @else
                            <input type="number" min="0" max="{{$task->max_rate}}" name="task_{{$task->id}}" class="rate_input" value="0">
                        @endif
                        / {{$task->max_rate}}
                    </td>
                @endforeach
            </tr>
            </tbody>
        </table>
        <br>
        <hr>
        <div class="form_wrapper">
            <div class="form_submit-field">
                <button type="submit" class="submit-button">Сохранить</button>
            </div>
        </div>
        <hr>
        <br>

    </form>


    <h1>Задания</h1>
    <hr>

    @foreach($tasks as $task)
        <h1><small>Задание {{$task->number}}.</small></h1><br>
        {{$task->task_text}}
        <br>
        <h1><small>Решение.</small></h1>
        {{$task->task_answer}}
        <br><br>
        <hr>
        <br>
    @endforeach

@endsection

@section('scripts')
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script type="module" src="{{ asset('js/creator/contest/place/index.js') }}"></script>
@endsection
