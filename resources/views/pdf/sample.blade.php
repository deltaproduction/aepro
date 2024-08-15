<!DOCTYPE html>
<html>
<head>
    <title>s</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <style>
        html, body {
            margin: 50px;
            margin-top: 20px;
            font-family: 'Open Sans', sans-serif;
        }
        h1, h3 {
            margin: 0;
        }
        .title {
            position: absolute;
            left: 0px;
        }
        .info {
            position: absolute;
            right: 0px;
            top: 40px;
            text-align: right;
        }

        .notification_text {
            font-weight: bold;
            font-size: 1.3em;
        }

        .contest_title {
            font-size: 2.3em;
        }

        .separator1 {
            position: absolute;
            left: 0;
            top: 60px;
            width: 100%;
        }

        .reg_number_text {
            font-size: 1em;
            font-style: italic;
        }

        .reg_number {
            position: absolute;
            font-size: 2.7em;
            top: 6px;
            right: -4px;
            letter-spacing: 2px;
            border: 2px solid grey;
            margin: 0;
            padding: 0;
        }

        .content {
            position: absolute;
            left: 0;
            top: 160px;
            font-size: 1em;
        }

        .info_text {
            font-size: 1.3em;
            letter-spacing: 4px;
        }

        .property_text {
            color: grey;
        }
    </style>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    <div class="head">
        <div class="title">
            <span class="notification_text">УВЕДОМЛЕНИЕ</span>
            <span>участника</span><br>
            <span class="contest_title">
                {{$title}}
            </span>
        </div>
        <div class="info">
            <span class="reg_number_text">Регистрационный номер</span>
            <div class="reg_number">{{$reg_number}}</div>
        </div>
    </div>
    <div class="content">
        <table>
            <tr>
                <td width="180px" class="property_text">Фамилия</td>
                <td class="info_text">Османов</td>
            </tr>
            <tr>
                <td class="property_text">Имя</td>
                <td class="info_text">Осман</td>
            </tr>
            <tr>
                <td class="property_text">Отчество</td>
                <td class="info_text">Арсаналиевич</td>
            </tr>
        </table>
        <br><br>
        <table>
            <tr>
                <td width="180px" class="property_text">Уровень</td>
                <td class="info_text">8 класс</td>
            </tr>
            <tr>
                <td class="property_text">Пункт проведения</td>
                <td class="info_text">МБОУ "Лицей №39"</td>
            </tr>
            <tr>
                <td class="property_text">Местность</td>
                <td class="info_text">Махачкала</td>
            </tr>
            <tr>
                <td class="property_text">Адрес</td>
                <td class="info_text">проспект Имама Шамиля, 47</td>
            </tr>
        </table>
    </div>


    <hr class="separator1">

</body>
</html>
