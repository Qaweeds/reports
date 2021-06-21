@extends('layouts.main')
@section('title')Реализаторы@endsection
@section('content')
    <main id="rr-main">
        <a href="{{url('/')}}"><h1>Реализаторы</h1></a>
        <button type="button" class="print-button btn btn-outline-primary" onclick="printpage()">Печать</button>
        <div id="box"></div>
        @foreach($data as $city => $table)
            <div class="city-table">
                <h3>{{$city}}</h3>
                <div class="table-wrap">
                    @php echo $table; @endphp
                </div>
            </div>
        @endforeach
    </main>
@endsection
@section('script')
@section('script')
    {{--<script src="js/name.js"></script>--}}
@endsection

