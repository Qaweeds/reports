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
@endsection

