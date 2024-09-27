@extends('layouts.creator')

@section('title', 'Home Page')

@section('mw-title', 'Новая аудитория')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/creator/rating.css') }}">
@endsection

@section('container')
    <small class="path">
        <span><a href="/">Главная</a> / Мои испытания / <a href="/contest/{{$contest_id}}">{{$contest_title}}</a> / </span> Рейтинг
    </small>
    <hr><br>


    <div class="header">
        <div>
            <h1>Рейтинг</h1>
            <small>всех участников испытания</small><br><br>
        </div>
        <div class="info">
            <div class="side">
                <div class="buttons">
                    <a href="rating/download">
                        <div class="download-button">
                            <div class="download-button__wrapper">
                                <div class="download-button__image"></div>
                                <div class="download-button__text">
                                    <span>Файл Excel</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <hr><br>

    <div class="search-filter">
        <input type="hidden" name="contest_id" value="{{$contest_id}}">

        <div class="search-field form_field">
            <div>Уровень:</div>
            <div>
                <select id="level">
                    <option value="0">Все уровни</option>

                    @foreach ($levels as $level)
                        <option value="{{$level->id}}">{{$level->title}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="search-field form_field">
            <div>Площадка:</div>
            <div>
                <select id="place">
                    <option value="0">Все площадки</option>

                    @foreach ($places as $place)
                        <option value="{{$place->id}}">{{$place->title}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <hr>

    <br><br><br>
    <table class="rating_table"
           @if ($contest_members_count == 0)
               style="display: none;"
        @endif
    >
        <thead>
        <tr>
            <th style="width: 5%">№</th>
            <th style="width: 37%">ФИО</th>
            <th style="width: 35%">Школа</th>
            <th style="width: 15%">Уровень</th>
            <th style="width: 8%">ИТОГО</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($contest_members as $index => $contest_member)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{$contest_member->last_name}} {{$contest_member->first_name}} {{$contest_member->middle_name}}</td>
                <td>
                    @if ($contest_member->school_name)
                        {{$contest_member->school_name}}

                    @else
                        {{$contest_member->short_title}}
                    @endif
                </td>
                <td>{{$contest_member->title}}</td>
                <td>{{$contest_member->grades()->sum('final_score')}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="not-found_message"
         @if ($contest_members_count != 0)
             style="display: none;"
        @endif
    >По заданным критериям результатов не найдено.</div>


    <br><br><br><br><br>

@endsection

@section('scripts')
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script type="module" src="{{ asset('js/creator/contest/rating.js') }}"></script>
@endsection
