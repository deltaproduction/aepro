@extends('layouts.creator')

@section('title', 'Home Page')

@section('container')
<small class="path">
    <span><a href="/">Главная</a> / Мои испытания / <a href="/contest/{{$contest_id}}">{{$contest_title}}</a> / Уровни / <a href="/contest/{{$contest_id}}/level/{{$level_id}}">{{$level_title}}</a> / </span>Задание №{{$task_number}}
</small>
<hr><br>

<div class="header">
    <div>
        <h1>Задание №{{$task_number}}</h1>
        <small>Номер задания</small><br><br>
    </div>
</div>

<hr style="margin-bottom: 0;">

<div class="tasks">
    <div class="tasks_wrapper">
        <div class="tasks_sidebar">
            <div class="tasks_sidebar-header">
                <div class="tasks_sidebar-header__title">
                    Прототипы
                </div>

                <div class="tasks_sidebar-header__new"></div>
            </div>
            <hr style="margin: 0;">
            <div class="tasks_sidebar-items">
                @foreach ($task_prototypes as $task_prototype)
                    <div class="tasks_sidebar-item" tp-id="{{$task_prototype->id}}">
                        {{$task_prototype->prototype_number}}
                    </div>
                @endforeach
            </div>
        </div>
        <div class="tasks_content">
            <div class="tasks_content_wrapper">
                <p>Условие</p>
                <div id="task-editor" style="height: 200px; width: 100%;">@if (!$task_prototypes->isEmpty()){{$task_prototypes[0]->task_text}}@endif</div>
                <br>
                <p>Решение с ответом</p>
                <div id="answer-editor" style="height: 300px; width: 100%;">@if (!$task_prototypes->isEmpty()){{$task_prototypes[0]->task_answer}}@endif</div>
                <br>
                <hr>

                <form id="saveData">
                    <input type="hidden" name="contest_id" value="{{$contest_id}}">
                    <input type="hidden" name="level_id" value="{{$level_id}}">
                    <input type="hidden" name="task_id" value="{{$task_id}}">
                    <input type="hidden" name="tp_id" @if (!$task_prototypes->isEmpty())
                    value="{{$task_prototypes[0]->id}}"
                    @endif>

                    <div class="form_wrapper">
                        <div class="form_submit-field">
                            <button type="submit" class="submit-button">Сохранить</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>


@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/creator/task.css') }}">
@endsection

@section('scripts')
<script type="module" src="{{ asset('js/ace/ace.js') }}"></script>
<script type="module" src="{{ asset('js/ace/theme-tomorrow.js') }}"></script>
<script type="module" src="{{ asset('js/ace/mode-latex.js') }}"></script>

<script type="text/javascript">
// @if (!$task_prototypes->isEmpty())
// let activeItem = {{$task_prototypes[0]->id}};
// @endif
// let taskPrototypes = {
//     @foreach ($task_prototypes as $task_prototype)
//         {{$task_prototype->id}}: [@json($task_prototype->prototype_number), @json($task_prototype->task_text), @json($task_prototype->task_answer)],
//     @endforeach
// };
</script>

<script type="module" src="{{ asset('js/creator/contest/place/task.js') }}"></script>
@endsection
