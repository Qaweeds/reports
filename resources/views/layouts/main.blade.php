<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1,minimum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
          integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href="css/all.css">
    <link rel="icon" href="storage/favicon.jfif">
    <script src="js/main.js"></script>
    <title>@yield('title')</title>

    @yield('head')

</head>
<body>

{{--<header class="d-flex justify-content-center py-3">--}}
    {{--<ul class="nav nav-pills">--}}
        {{--<li class="nav-item"><a class="nav-link @if(url()->current() == url('/Kh')) active @endif" href="{{url('/Kh')}}">Харьков</a></li>--}}
        {{--<li class="nav-item"><a class="nav-link @if(url()->current() == url('/Od')) active @endif" href="{{url('/Od')}}">Одесса</a></li>--}}
        {{--<li class="nav-item"><a class="nav-link @if(url()->current() == url('/Khm')) active @endif" href="{{url('/Khm')}}">Хмельницкий</a></li>--}}
        {{--<li class="nav-item dropdown">--}}
            {{--<a class="nav-link dropdown-toggle" href="#" id="dropdown10"--}}
               {{--data-bs-toggle="dropdown" aria-expanded="false">Отчеты</a>--}}
            {{--<ul class="dropdown-menu" aria-labelledby="dropdown10">--}}
                {{--<li><a class="dropdown-item" href="{{route('report')}}">Реазилаторы</a></li>--}}
                {{--<li><a class="dropdown-item" href="/test2">Реализаторы v.2</a></li>--}}
            {{--</ul>--}}
        {{--</li>--}}
    {{--</ul>--}}
{{--</header>--}}
@yield('content')
</body>
@yield('script')
</html>