@extends('layouts.main')
@section('head')
    <link rel="stylesheet" href="css/hats.css">
    <script src="js/hats.js"></script>
@endsection
@section('title')Шапки @endsection
@section('content')
    <div style="margin: -74px 0 0 260px;
        width: 494px; overflow: auto;
    ">
        {!! $table_header !!}
    </div>
    <div class="ml-1">
        {!! $table !!}
    </div>
    <p style="position: absolute;
                right: -200px;
                color: red;
                font-size: 25px;
                font-weight: bold;
                top: 176px;">
        Отчет находится в тестовом режиме.<br> Все цифры случайные.
    </p>
@endsection

