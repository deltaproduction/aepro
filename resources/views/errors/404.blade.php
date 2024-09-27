<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .image {
            width: 50%;
            height: 500px;
            background: url({{asset('images/404.svg')}});
            background-size: 100%;
        }
    </style>
</head>
<body>
<div class="image"></div>
</body>
<script>
    setInterval(function() {
        window.location.href = "/";
    }, 3000);
</script>
</html>
