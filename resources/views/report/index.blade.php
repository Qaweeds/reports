@extends('layouts.main')
@section('title')Реализаторы@endsection
@section('content')
    <main>
        <div id="wrong-date-alert" class="alert alert-danger"></div>

        <div id="info-wrap">
            <div id="box"></div>
            <span class="distributor-full-name"></span>
        </div>

        <div class="city-table">
            <div class="table-wrap">
                {!! $table !!}
            </div>

            <div class="tools-wrap">
                <button type="button" class="print-button btn btn-outline-primary" onclick="printpage()">Печать
                </button>
                <div class="date-form mb-2">
                    <form class="form-inline" method="get">
                        <label for="inlineFormInput"></label>
                        <input type="text" class="form-control mb-2 mr-sm-2 mb-sm-0" id="dateStart" name="D"
                               placeholder="Укажите дату" required>

                        <button type="submit" class="ml-2 btn btn-primary">Создать</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection
@section('script')
    <script>
        var names = @json($names);
    </script>
@endsection

