@extends('layouts.checker')

@section('title', 'Home Page')

@section('container')
<small class="path">
    <span><a href="/">Главная</a> / Мои проверки /</span> {{$contest_title}}
</small>
<hr><br>

<div class="header">
    <div>
        <h1>{{$contest_title}}</h1>
        <small>Название испытания</small><br><br>
    </div>
</div>

<hr style="margin-bottom: 0;">
@if ($at == 5)
	@if ($status)
	<div class="tasks">
	    <div class="tasks_wrapper">

            @if ($contest_members->count())
	        <div class="tasks_sidebar">
	            <div class="tasks_sidebar-header">
	                <div class="tasks_sidebar-header__title">
	                    Работы
	                </div>
	            </div>
	            <hr style="margin: 0;">
	            <div class="tasks_sidebar-items">
                    @foreach ($contest_members as $contest_member)
                        <a href="/contest/{{$contest_member->contest_id}}/check/{{$contest_member->id}}">
                            <div class="tasks_sidebar-item @if ($c_member) @if ($c_member->id == $contest_member->id) active @endif @endif">
                                {{$contest_member->reg_number}}
                            </div>
                        </a>
                    @endforeach
	            </div>
	        </div>
            @endif


	        <div class="tasks_content">
                @if ($c_member)
                    <div class="tasks_content_wrapper">
                            @if ($scans)
                                <h1>Сканы работы</h1>
                                <hr>
                                @foreach ($scans as $scan)
                                    <a href="/scan/{{$scan->path}}" target="_blank">Лист {{$scan->page_number}}</a><br>
                                @endforeach
                            @endif
                            <br><br>
                            <h1>Результат</h1>
                            <hr>
                            <p>Пожайлуста, убедитесь, что в ваших рассуждениях нет ошибок, и выставьте точный результат!</p>
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
                                                        <input type="number" min="0" max="{{$task->max_rate}}" name="task_{{$task->id}}" class="rate_input" value="{{$c_member->grades()->where("task_id", $task->id)->first()->score}}">
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
                            <br>

                        </form>

                        <div class="checking-managing_buttons">
                            <form action="{{route('refuseToWork')}}" method="POST">
                                @csrf
                                <input type="hidden" name="contest_id" value="{{$contest_id}}">
                                <input type="hidden" name="contest_member_id" value="{{$c_member->id}}">

                                <div class="form_wrapper">
                                    <div class="form_submit-field">
                                        <button type="submit" class="submit-button">Отказаться от работы</button>
                                    </div>
                                </div>
                            </form>

                            <form action="{{route('requestNewWork')}}" method="POST">
                                @csrf
                                <input type="hidden" name="contest_id" value="{{$contest_id}}">
                                <input type="hidden" name="expert_id" value="{{$expert_id}}">

                                <div class="form_wrapper">
                                    <div class="form_submit-field">
                                        <button type="submit" class="submit-button">Запросить новую работу</button>
                                    </div>
                                </div>
                            </form>
                        </div>


                            <br>
                            <h1>Задания работы</h1>
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
                    </div>

                @else
                    <div class="tasks_content_wrapper centered">
                        <div>
                            @if ($contest_members->count())
                            <p class="start-message">Для начала проверки выберите слева <br>одну из работ. Вместо этого сообщения <br>появятся сканы работ и будет предоставлена <br>возможность выставить баллы за задания.</p>
                            <form action="{{route('requestNewWork')}}" method="POST">
                                @csrf
                                <input type="hidden" name="contest_id" value="{{$contest_id}}">
                                <input type="hidden" name="expert_id" value="{{$expert_id}}">

                                <div class="form_wrapper">
                                    <div class="form_submit-field" style="justify-content: flex-start;">
                                        <button type="submit" class="submit-button">Запросить новую работу</button>
                                    </div>
                                </div>
                            </form>
                            @else
                                <p class="start-message">Запросите первую работу, нажав на кнопку ниже. <br>Все работы будут располагаться в сайдбаре слева.</p>
                                <form action="{{route('requestNewWork')}}" method="POST">
                                    @csrf
                                    <input type="hidden" name="contest_id" value="{{$contest_id}}">
                                    <input type="hidden" name="expert_id" value="{{$expert_id}}">

                                    <div class="form_wrapper">
                                        <div class="form_submit-field" style="justify-content: flex-start;">
                                            <button type="submit" class="submit-button">Запросить первую работу</button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif

	        </div>
	    </div>
	</div>
	@else
	<div class="message">

		<h2>Внимание!</h2>
		<hr>
		<p>
			Вы – эксперт испытания <b><u>{{$contest_title}}</u></b>.<br><br>Это означает, что вам вверена одна из самых главных задач<br>любого испытания — проверка работ. От вашей объективности<br>и справедливой оценки зависит успех каждого участника и<br>общее качество результатов.<br><br>Важно помнить, что ваши решения влияют не только на итоговые<br>баллы, но и на дальнейшую мотивацию и развитие тех, кто доверил<br>вам свои знания.<br><br><i>Будьте внимательны к <b>каждой</b> детали, следуйте установленным<br>критериям и сохраняйте беспристрастность в любой ситуации.</i>

		</p>

		<hr>

		<form method="POST" action="{{ route('agree') }}">
			<input type="checkbox" name="agree_accept">
			@csrf
		    <div class="form_field checkbox">
		        <label>
		          <input type="checkbox" name="agree_accept">
		          <span class="custom-checkbox"></span>
		        </label>
		        Подтверждаю, что буду ответственно и объективно подходить к проверке работ
		    </div>

		    <div class="form_submit-field">
		        <button type="submit" class="submit-button">Начать проверку</button>
		    </div>

	    </form>

	</div>







	@endif

@else

<p class="message">На этой странице будут появляться работы участников испытания <b><u>{{$contest_title}}</u></b>.<br><br>На данный момент этап загрузки работ участников на платформу ещё не завершен.<br>Как только он завершится, вы сможете вернуться на эту страницу и начать проверку работ.</p>

@endif


@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/checker/main.css') }}">
@endsection

@section('scripts')
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<script type="module" src="{{ asset('js/checker/index.js') }}"></script>
@endsection
