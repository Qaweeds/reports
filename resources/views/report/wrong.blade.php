@extends('layouts.main')
@section('title')Реализаторы@endsection
@section('content')
    <main id="rr-main">
        @if(Session::has('date-flip'))
            <div class="alert alert-danger">
                <div class="alert-heading">
                    {{ Session::get('date-flip') }}
                </div>
            </div>
        @endif
        <a href="{{url('/')}}"><h1>Реализаторы</h1></a>
        <button type="button" class="print-button btn btn-outline-primary" onclick="printpage()">Печать</button>
        <div class="date-form mb-2">
            <form class="form-inline" method="get" >
                @csrf
                <label for="inlineFormInput"></label>
                <input type="text" class="form-control mb-2 mr-sm-2 mb-sm-0" id="dateStart" name="D"
                       placeholder="Укажите дату">
                <button type="submit" class="ml-2 btn btn-primary">Создать</button>
            </form>
        </div>
        <div id="box"></div>
    </main>
@endsection
