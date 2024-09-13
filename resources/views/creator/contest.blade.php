@extends('layouts.creator')

@section('title', 'Home Page')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/creator/main.css') }}">
@endsection

@section('mw-title', '')

@section('mw-content')
<div class="mw-newPlace">
    <form id="newPlace_form">
        <input type="hidden" name="contest_id" value="{{$contest_id}}">

        <div class="form_wrapper">
            <div class="form_field">
                <div class="">
                    Название:
                </div>
                <div class="">
                    <input type="text" name="place_title" autocomplete="off" placeholder="Обязательное поле">
                </div>
            </div>

            <div class="form_field">
                <div class="">
                    Местность:
                </div>
                <div class="">
                    <input type="text" name="place_locality" autocomplete="off" placeholder="Обязательное поле">
                </div>
            </div>

            <div class="form_field">
                <div class="">
                    Адрес:
                </div>
                <div class="">
                    <input type="text" name="place_address" autocomplete="off" placeholder="Необязательное поле">
                </div>
            </div>

            <div class="form_submit-field">
                <button type="submit" class="submit-button">Добавить</button>
            </div>

        </div>
    </form>
</div>
<div class="mw-newLevel">
    <form id="newLevel_form">
        <input type="hidden" name="contest_id" value="{{$contest_id}}">

        <div class="form_wrapper">
            <div class="form_field">
                <div class="">
                    Название:
                </div>
                <div class="">
                    <input type="text" name="level_title" autocomplete="off" placeholder="Обязательное поле">
                </div>
            </div>

            <div class="form_submit-field">
                <button type="submit" class="submit-button">Добавить</button>
            </div>

        </div>
    </form>
</div>
<div class="mw-newExpert">
    <form id="newExpert_form">
        <input type="hidden" name="contest_id" value="{{$contest_id}}">

        <div class="form_wrapper">
            <div class="form_field">
                <div class="">
                    ФИО:
                </div>
                <div class="">
                    <input type="text" name="expert_name" autocomplete="off" placeholder="Обязательное поле">
                </div>
            </div>

            <div class="form_field">
                <div class="">
                    E-mail:
                </div>
                <div class="">
                    <input type="text" name="expert_email" autocomplete="off" placeholder="Обязательное поле">
                </div>
            </div>

            <div class="form_field">
                <div class="">
                    Уровень:
                </div>
                <div class="">
                    <select class="" name="expert_level">
                        @foreach ($levels as $level)
                            <option value="{{$level->id}}">{{$level->title}}</option>
                        @endforeach
                    </select>
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
    <span><a href="/">Главная</a> / Мои испытания / </span>{{$title}}
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

            <div class="buttons">
                @if ($at == 4)
                    <a href="{{$contest_id}}/ppi_files">
                        <div class="download-button">
                            <div class="download-button__wrapper">
                                <div class="download-button__image"></div>
                                <div class="download-button__text">
                                    <span>Файлы ППИ</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>


<hr><br>
<h1>Площадки</h1>
<hr>
<div class="cards_block">
    @foreach ($places as $place)
    <a href="{{$contest_id}}/place/{{$place->id}}">
        <div class="card">
            <div class="card_wrapper">
                <h1>{{$place->title}}</h1>

                <div>
                    <p><b>Местность:</b> {{$place->locality}}</p>
                    <p><b>Адрес:</b>  {{$place->address}}</p>
                </div>
            </div>
        </div>
    </a>
    @endforeach
    <div>
        <div class="card new-card new-place">
            <div class="card_wrapper"></div>
        </div>
    </div>
</div>

<br><br>
<h1>Уровни</h1>
<hr>
<div class="cards_block">
    @foreach ($levels as $level)
    <a href="{{$contest_id}}/level/{{$level->id}}">
        <div class="card">
            <div class="card_wrapper">
                <h1>{{$level->title}}</h1>
            </div>
        </div>
    </a>
    @endforeach
    <div>
        <div class="card new-card new-level">
            <div class="card_wrapper"></div>
        </div>
    </div>
</div>

<br><br>

<h1>@if ($at != 5) Управление испытанием @else Управление проверкой работ @endif</h1>
<hr>

<input type="hidden" name="contest_id" value="{{$contest_id}}">

<div class="form_wrapper">
@if ($at == 0)
    <div class="form_field">
        <button class="submit-button start-apply">Начать прием</button>
    </div>

@elseif ($at == 1)
    <div class="form_field">
        <button class="submit-button stop-apply">Приостановить прием</button>
    </div>
    <div class="form_field">
        <button class="submit-button end-apply">Завершить прием</button>
    </div>
@elseif ($at == 2)
    <div class="form_field">
        <button class="submit-button start-apply">Возобновить прием</button>
    </div>
    <div class="form_field">
        <button class="submit-button end-apply">Завершить прием</button>
    </div>

@elseif ($at == 3)
    Участники испытания распределены по аудиториям и по своим местам.

    <div class="form_field">
        <button class="submit-button start-generation">Запустить генерацию файлов</button>
    </div>
    <br>
    <table class="statuses_table">
        <thead>
            <tr>
                <th style="width: 45%">Тип</th>
                <th style="width: 55%">Статус</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Варианты</td>
                <td>
                    @if ($options_status === null)
                    Ожидание
                    @elseif ($options_status === 0)
                    В процессе...
                    @elseif ($options_status === 1)
                    Готово
                    @elseif ($options_status === 2)
                    Ошибка
                    @endif
                </td>
            </tr>

            <tr>
                <td>Работы</td>
                <td>
                    @if ($auditoriums_status === null)
                    Ожидание
                    @elseif ($auditoriums_status === 0)
                    В процессе...
                    @elseif ($auditoriums_status === 1)
                    Готово
                    @elseif ($auditoriums_status === 2)
                    Ошибка
                    @endif
                </td>
            </tr>

            <tr>
                <td>Протоколы</td>
                <td>
                    @if ($protocols_status === null)
                    Ожидание
                    @elseif ($protocols_status === 0)
                    В процессе...
                    @elseif ($protocols_status === 1)
                    Готово
                    @elseif ($protocols_status === 2)
                    Ошибка
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <div class="form_field">
        <button class="submit-button end-tour">Завершить тур</button>
    </div>

@elseif ($at == 4)
Тур завершен. Загрузите все файлы аудиторий (ACS-файлы) и нажмите "Отправить".
<div class="form_field-big">
    <div>Файлы аудиторий:</div>
    <div class="file-field"><input type="file" name="auditoriums_files" multiple></div>
</div>
<div class="form_field">
    <button class="submit-button send-files">Отправить</button>
</div>

<p class="after-sending_information">Загружено работ: <span id="papers_loaded">4</span><br>
Загружено сканов: <span id="scans_loaded">4</span></p>

<div class="form_field">
    <button class="submit-button start-checking">Запустить проверку</button>
</div>


@elseif ($at == 5)
До конца основного этапа остался один шаг – проверка работ. Не волнуйтесь: работы уже распределены по экспертам. Остаётся только ждать :)


@endif

</div>

<br><br><br>

<h1>Эксперты</h1>
<hr>
<p>Добавьте экспертов для проверки и назначьте каждому уровень работы.</p>
<br>
<p>
    <a href="javascript:void(0);" class="void-link new-expert_link">Добавить нового эксперта</a>
</p>

<table class="experts_table">
	<thead>
		<tr>
			<th style="width: 5%">№</th>
			<th style="width: 50%">ФИО</th>
			<th style="width: 25%">E-mail</th>
			<th style="width: 15%">Уровень</th>
            <th style="width: 5%"></th>
		</tr>
	</thead>
	<tbody>
        @foreach ($experts as $index => $expert)
    		<tr>
    			<td>{{ $index + 1 }}</td>
    			<td>{{ $expert->name }}</td>
    			<td>{{ $expert->email }}</td>
    			<td>{{ $expert->title }}</td>
                <td>X</td>
    		</tr>
		@endforeach
	</tbody>
</table>

<br><br>

<h1>Апелляции</h1>
<hr>
Список

<br><br>

@endsection

@section('scripts')
<script type="module" src="{{ asset('js/creator/contest/index.js') }}"></script>
@endsection
