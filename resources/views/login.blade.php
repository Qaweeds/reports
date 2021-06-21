@extends('layouts.main')


@section('content')
    <div class="container">

        <div id="login-form">
            <div class="row">
                <div class="col">

                    <form action="{{ route('login.login') }}" method="POST">
                        <input type="hidden" name="_method" value="POST">
                        @csrf
                        @if($errors->any())
                            <div class="m-auto">
                                @foreach ($errors->all() as $error)
                                    <p style="color:red; text-align: center">{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="login">Логин</label>
                                <input type="text" class="form-control" id="login" name="login">
                            </div>
                            <div class="form-group col-12">
                                <label for="password">Пароль</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>

                        </div>


                        <div class="form-row">
                            <div class="col-12">
                                <button class="btn btn-primary btn-block">Войти</button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')

@endsection


