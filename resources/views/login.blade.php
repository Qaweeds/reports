<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
          integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <title>Отчеты</title>
</head>
<body>
<div class="container" style="width: 400px;">
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

                    <div class="form-group row">
                        <div class="col-md-6 offset-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="remember">
                                    {{ __('Запомнить') }}
                                </label>
                            </div>
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
</body>
</html>


