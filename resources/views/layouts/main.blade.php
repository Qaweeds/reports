<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1,minimum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
          integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href="css/all.css">
    <link rel="icon" href="storage/favicon.jfif">
    <script src="js/main.js"></script>
    <title>@yield('title')</title>

    @yield('head')

</head>
<body>
<div id="rr-main">
    <div class="dropdown" style="width: 260px;">
        <h1 class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false" style="color: #007bff;">
            @if(request()->path() == '/') Реализаторы (Опт) @endif
            @if(request()->path() == 'all') Реализаторы (Все) @endif
            @if(request()->path() == 'hats') Шапки @endif
        </h1>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            @if(request()->path() != '/')<a class="dropdown-item" href="{{route('report')}}"><h2>Реализаторы (Опт)</h2></a> @endif
                @if(request()->path() != 'all')<a class="dropdown-item" href="{{route('report_retail')}}"><h2>Реализаторы (Все)</h2></a> @endif
                @if(request()->path() != 'hats')<a class="dropdown-item" href="{{route('hats.create')}}"><h2>Шапки</h2></a> @endif
        </div>
    </div>
    @yield('content')
</div>

</body>
@yield('script')
</html>