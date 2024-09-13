<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title></title>
        <style>
            * {
                font-family: "Inter", sans-serif;

                font-size: .9em;
            }
            hr {
                border: .4px solid black;
                width: 100%;
            }

            small {
                color: #cccccc;
            }

            .content {
                width: 100%;
            }

            .header {
                position: absolute;
                width: 100%;
            }

            .auditorium_title {
                position: absolute;
                left: 0;
            }

            .ppi_title {
                position: absolute;
                right: 0;
            }

            .separator1 {
                position: absolute;
                top: 15px;
            }

            .separator2 {
                position: absolute;
                bottom: 55px;
            }

            .start_text {
                position: absolute;
                right: 150px;
                top: 47px;
            }

            .end_text {
                position: absolute;
                right: 155px;
                top: 96px;
            }

            .fp {
                position: absolute;
                width: 24px;
                border: 1px solid black;
                height: 30px;
            }

            .fp1 {
                top: 39px;
            }

            .fp2 {
                top: 90px;
            }

            .field_part1 {
                right: 112px;
            }

            .field_part2 {
                right: 87px;
            }

            .field_part3 {
                right: 50px;
            }

            .field_part4 {
                right: 25px;
            }

            .colon1 {
                position: absolute;
                top: 42px;
                right: 80px;
                font-size: 1.4em;
            }

            .colon2 {
                position: absolute;
                top: 92px;
                right: 80px;
                font-size: 1.4em;
            }

            center.main_title {
                position: relative;
                top: 160px;
                font-size: 1em;
                font-weight: 500;
            }

            table {
                border-collapse: collapse;
                border: .5px solid black;
                width: 100%;
                position: relative;
                top: 200px;
            }

            table td {
                padding-left: 4px;
                height: 13px;
                border: .5px solid black;
            }

            .first_line td {
                height: 25px;
                text-align: center;
                margin: 0;
                border-bottom: 1px solid black;
            }

            .org_1_text {
                position: absolute;
                bottom: 30px;

                font-size: .9em;
            }

            .org_2_text {
                position: absolute;
                bottom: 0px;

                font-size: .9em;
            }

            .sign1 {
                position: absolute;
                bottom: 30px;
                right: 0;
            }

            .sign2 {
                position: absolute;
                bottom: 0px;
                right: 0;
            }

            .sign1 span, .sign2 span {
                font-style: italic;
                color: lightgrey;
                font-size: .8em;
            }
        </style>
    </head>
    <body>

        <div class="content">
            <div class="header">
                <div class="auditorium_title">
                    Аудитория {{$auditorium_title}}
                </div>

                <div class="ppi_title">
                    ППИ {{$ppi_number}}
                </div>
            </div>
            <hr class="separator1">
            <div class="time">
                <div class="start_text">Начало:</div>
                <div class="end_text">Конец:</div>

                <div class="colon1">:</div>
                <div class="colon2">:</div>

                <div class="fp fp1 field_part1"></div>
                <div class="fp fp1 field_part2"></div>
                <div class="fp fp1 field_part3"></div>
                <div class="fp fp1 field_part4"></div>

                <div class="fp fp2 field_part1"></div>
                <div class="fp fp2 field_part2"></div>
                <div class="fp fp2 field_part3"></div>
                <div class="fp fp2 field_part4"></div>
                <div></div>
            </div>

            <center><small>Испытание</small> {{$contest_title}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small>Уровень</small> {{$level_title}}</center>

            <center class="main_title">ПРОТОКОЛ ПРОВЕДЕНИЯ ИСПЫТАНИЯ</center>

            <table border="1">
                <tr class="first_line">
                    <td valign="middle" style="width: 4%;">№</td>
                    <td valign="middle" style="width: 7%;">Рег.номер</td>
                    <td valign="middle" style="width: 44%;">Школа</td>
                    <td valign="middle" style="width: 5%;">Место</td>
                    <td valign="middle" style="width: 6%;">Вариант</td>
                    <td valign="middle" style="width: 10%;">Время окончания</td>
                    <td valign="middle" style="width: 5%;">Н/я</td>
                    <td valign="middle" style="width: 6%;">Б</td>
                    <td valign="middle" style="width: 6%;">З</td>
                    <td valign="middle" style="width: 5%;">Н/з</td>
                    <td valign="middle" style="width: 6%;">Подпись</td>
                </tr>

                @foreach ($contest_members as $num => $contest_member)
                    <tr>
                        <td style="text-align: right;">{{$num + 1}}.&nbsp;&nbsp;</td>
                        <td>{{$contest_member->reg_number}}</td>
                        <td>
                            @if ($contest_member->school_name)
                                {{$contest_member->school_name}}

                            @else
                                {{$contest_member->short_title}}

                            @endif


                        </td>
                        <td style="text-align: center;">{{$contest_member->seat}}</td>
                        <td style="text-align: center;">{{$contest_member->variant}}</td>
                        <td></td>
                        <td style="background: #f0f0f0;"></td>
                        <td></td>
                        <td></td>
                        <td style="background: #f0f0f0;"></td>
                        <td></td>
                    </tr>
                @endforeach


            </table>



            <hr class="separator2">

            <div class="org_1_text">
                Организатор 1
            </div>

            <div class="org_2_text">
                Организатор 2
            </div>

            <div class="sign1">
                _________________________________________________________________________________ / &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>подпись</span>
            </div>

            <div class="sign2">
                _________________________________________________________________________________ / &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>подпись</span>
            </div>
        </div>

    </body>
</html>
