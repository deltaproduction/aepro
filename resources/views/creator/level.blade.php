@extends('layouts.creator')

@section('title', 'Home Page')

@section('mw-content')
<div class="mw-newTask">
    <form id="newTask_form">
        <input type="hidden" name="contest_id" value="{{$contest_id}}">
        <input type="hidden" name="level_id" value="{{$level_id}}">
        <div class="form_wrapper">
            <div class="form_field">
                <div class="">
                    Номер:
                </div>
                <div class="">
                    <input type="text" disabled value="{{$tasks_count + 1}}">
                </div>
            </div>

            <div class="form_field">
                <div class="">
                    Макс.балл:
                </div>
                <div class="">
                    <input type="number" name="task_max_rate" min="1" autocomplete="off" placeholder="Обязательное поле">
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
    <span><a href="/">Главная</a> / Мои испытания / <a href="/contest/{{$contest_id}}">{{$contest_title}}</a> / Уровни / </span>{{$title}}
</small>
<hr><br>

<div class="header">
    <div>
        <h1>{{$title}}</h1>
        <small>Название уровня</small><br><br>
    </div>
</div>
<hr>
<br>

<h1>Апелляция</h1>
<hr>
<br>
<div class="form_wrapper">
    @if ($appeal)
        <div class="form_field">
            <button class="submit-button stop-appeals">Остановить приём апелляций</button>
        </div>

    @else
        <div class="form_field">
            <button class="submit-button start-appeals">Возобновить приём апелляций</button>
        </div>
    @endif
</div>

<br><br><br>

<h1>Шаблон</h1>
<hr>

<div class="form_wrapper">
    <input type="hidden" name="contest_id" value="{{$contest_id}}">
    <input type="hidden" name="level_id" value="{{$level_id}}">

    <div class="form_field">
        <div id="latex-editor" style="height: 400px; width: 100%;">{{$pattern}}</div>
    </div>
    <hr>
    <div class="form_submit-field">
        <button class="submit-button save-pattern">Сохранить шаблон</button>
    </div>
</div>

<br>
<br>
<h1>Банк заданий</h1>
<hr>
<div class="cards_block">
    @foreach ($tasks as $task)
    <a href="{{$level_id}}/task/{{$task->id}}">
        <div class="card">
            <div class="card_wrapper">
                <h1>Задание {{$task->number}}</h1>
                <div>
                    <p><b>Макс.балл:</b> {{$task->max_rate}}</p>
                </div>
            </div>
        </div>
    </a>
    @endforeach
    <div>
        <div class="card new-card new-task">
            <div class="card_wrapper"></div>
        </div>
    </div>
</div>
<br><br><br>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.14/ace.js"></script>
<script type="module" src="{{ asset('js/creator/level/index.js') }}"></script>
@endsection
