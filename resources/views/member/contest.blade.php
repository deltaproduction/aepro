@extends('layouts.member')

@section('title', 'Home Page')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/member/main.css') }}">
@endsection


@section('container')
<small class="path">
    <span><a href="/">Главная</a> / Мои участия / </span>{{$title}}
</small>

<hr><br>

<div class="header">
    <div>
        <h1>{{$title}}</h1>
        <small>Название испытания</small><br><br>
    </div>
    <div class="info">
        <div class="side">
            <div>
                <small>Код испытания</small>
                <div class="contest-code">
                    @for ($i = 0; $i < strlen($contest_code); $i++)
                        <div class="letter">
                            {{$contest_code[$i]}}
                        </div>
                    @endfor

                </div>
            </div>

            <div>
                <small>Регистрационный номер</small>
                <div class="contest-code">
                    @for ($i = 0; $i < strlen($reg_number); $i++)
                        <div class="letter">
                            {{$reg_number[$i]}}
                        </div>
                    @endfor

                </div>
            </div>

            <div class="buttons">
                <a href="notification">
                    <div class="download-button">
                        <div class="download-button__wrapper">
                            <div class="download-button__image"></div>
                            <div class="download-button__text">
                                <span>Уведомление</span>
                            </div>
                        </div>
                    </div>
                </a>

                @if ($at >= 4)
                <a href="option">
                    <div class="download-button">
                        <div class="download-button__wrapper">
                            <div class="download-button__image"></div>
                            <div class="download-button__text">
                                <span>Работа</span>
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

<h1>Информация о площадке</h1>
<hr>

<pre><b>Уровень:</b>		<span>{{$level}}</span></pre>
<pre><b>Местность:</b>		<span>{{$locality}}</span></pre>
<pre><b>Площадка:</b>		<span>{{$place}}</span></pre>
@if ($address)
<pre><b>Адрес:</b>			<span>{{$address}}</span></pre>
@endif
<br><br>

<hr><br>

<h1>Информация об участии в испытании</h1>
<hr>

@if ($auditorium)
<pre><b>Аудитория:</b>				<span>{{$auditorium}}</span></pre>
@endif

@if ($seat)
<pre><b>Место:</b>					<span>{{$seat}}</span></pre>
@endif

@if ($option)
<pre><b>Вариант:</b>				<span>{{$option}}</span></pre>
@endif

@if ($absence)
<pre><b>Явился:</b>					<span>Нет</span></pre>
@else
<pre><b>Явился:</b>					<span>Да</span></pre>
@endif

@if ($not_finished)
<pre><b>Завершил работу:</b>		<span>Нет</span></pre>
@else
<pre><b>Завершил работу:</b>		<span>Да</span></pre>
@endif

@if ($end_time)
<pre><b>Время завершения:</b>		<span>{{$end_time}}</span></pre>
@endif

@if ($blanks)
<pre><b>Количество бланков:</b>		<span>{{$blanks}}</span></pre>
@endif

@if ($tasks)
<pre><b>Выполнено заданий:</b>		<span>{{$tasks}}</span></pre>
@endif


<br><br>


@if ($scans)
<h1>Сканы работы</h1>
<hr>
@foreach ($scans as $scan)
<a href="/scan/{{$scan->path}}">Лист {{$scan->page_number}}</a><br>
@endforeach
@endif


@endsection

@section('scripts')
<script type="module" src="{{ asset('js/member/index.js') }}"></script>
@endsection
