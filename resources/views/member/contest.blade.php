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
                    @for ($i = 0; $i < 7; $i++)
                        <div class="letter">
                            {{$contest_code[$i]}}
                        </div>
                    @endfor

                </div>
            </div>

            <div>
                <small>Регистрационный номер</small>
                <div class="contest-code">
                    @for ($i = 0; $i < 9; $i++)
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

                <a href="notification">
                    <div class="download-button">
                        <div class="download-button__wrapper">
                            <div class="download-button__image"></div>
                            <div class="download-button__text">
                                <span>Работа</span>
                            </div>
                        </div>
                    </div>
                </a>

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




@endsection

@section('scripts')
<script type="module" src="{{ asset('js/member/index.js') }}"></script>
@endsection
