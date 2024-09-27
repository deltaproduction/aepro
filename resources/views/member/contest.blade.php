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
<a href="/scan/{{$scan->path}}" target="_blank">Лист {{$scan->page_number}}</a><br>
@endforeach
@endif

@if (!($not_finished or $absence))
<br><br>
<h1>Результаты</h1>
<hr>
<br>

@if ($publish)

    @if (count($grades))
<table>
    <thead>
    <tr>
        <th>Задание</th>
        <th>Получено</th>
        <th>Макс.балл</th>
    </tr>
    </thead>
    <tbody>

        @foreach($grades as $grade)
            <tr>
                <td>{{$grade->number}}</td>
                <td>{{$grade->final_score}}</td>
                <td>{{$grade->max_rate}}</td>
            </tr>
        @endforeach

    </tbody>
</table>

<div class="result-block">
    <span class="text">ИТОГО:</span>
    <span class="result"><b>{{$grades->sum("final_score")}}</b></span> / <span class="text">{{$grades->sum("max_rate")}}</span>
</div>
    @else
        Результатов нет.
    @endif

    <br><br><br>

    @if ($appeal_allowed or $appeal !== null)
    <h1>Апелляция</h1><hr>
    <br>
    @if ($appeal === null)
    Если вы не согласны с результатами или по некоторым другим причинам вы хотите подать апелляцию, вы можете заполнить форму ниже.
    <br><br>
    <h1 style="color: darkred;">Внимание!</h1>
    <hr>
    <p style="line-height: 1.6em;">
        Апелляция подаётся только один раз!<br>Изменить содержание претензий или контактные данные<br>после отправки будет невозможно.
        <br><br>Пожалуйста, перепроверяйте текст апелляции.
    </p>
    <br><br><br>
    @endif
    <div class="appeal-form">
        <form action="{{route("sendAppeal")}}" method="POST">
            @csrf
            <input type="hidden" name="c_member" value="{{$c_member_id}}">
            <div class="form_wrapper">
                <div class="form_field">
                    <div class="">
                        E-mail для связи:
                    </div>
                    <div class="">

                        @if ($appeal === null)
                            <input type="text" name="email" required placeholder="{{auth()->user()->email}}" value="{{auth()->user()->email}}" autocomplete="off">
                        @else
                            <input type="text" value="{{$appeal->email}}" readonly>
                        @endif
                    </div>
                </div>

                <div class="form_field">
                    <div class="">
                        Номер телефона:
                    </div>
                    <div class="">
                        @if ($appeal === null)
                            <input type="text" name="phone" required placeholder="Обязательное поле" autocomplete="off">
                        @else
                            <input type="text" value="{{$appeal->phone}}" readonly>
                        @endif
                    </div>
                </div>

                <div class="form_field-big">
                    <div class="">
                        Сообщение:
                    </div>
                    <div class="">
                        @if ($appeal === null)
                            <textarea name="appeal_text" rows="10" required></textarea>
                        @else
                            <textarea rows="10" readonly>{{$appeal->text}}</textarea>
                        @endif
                    </div>
                </div>
                @if ($appeal === null)
                    <div class="form_submit-field" style="width: calc(200% + 26px);">
                        <button type="submit" class="submit-button">Отправить апелляцию</button>
                    </div>
                @endif
            </div>
        </form>
    </div>
    @endif

@else
Ваша работа пока не проверена или результаты ещё не опубликованы.
@endif


@endif
<br><br><br><br><br><br><br><br>
@endsection

@section('scripts')
<script type="module" src="{{ asset('js/member/index.js') }}"></script>
@endsection
