@extends('layouts.main')
@section('head')<link rel="stylesheet" href="css/hats.css">@endsection
@section('title')Шапки @endsection
@section('content')

    <main>
        <div class="ml-1">
            {!! $table !!}

        </div>


    </main>
@endsection

