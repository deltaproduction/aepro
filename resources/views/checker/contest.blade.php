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
	        <div class="tasks_sidebar">
	            <div class="tasks_sidebar-header">
	                <div class="tasks_sidebar-header__title">
	                    Работы
	                </div>
	            </div>
	            <hr style="margin: 0;">
	            <div class="tasks_sidebar-items">
	                    <div class="tasks_sidebar-item" tp-id="">
	                        hey
	                    </div>
	            </div>
	        </div>
	        <div class="tasks_content">

	            <div class="tasks_content_wrapper">


	                <form id="saveData">
	                    <input type="hidden" name="contest_id" value="{{$contest_id}}">

	                    <input type="hidden" name="tp_id">

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

<script type="module" src="{{ asset('js/checker/index.js') }}"></script>
@endsection
