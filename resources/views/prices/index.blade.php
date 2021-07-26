<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
          integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href="css/prices.css">

    <title>Prices</title>
</head>
<body>
<main>
    {{--<input type="number" id="code" class="form-input">--}}
    {{--<button id="scroll" class="btn btn-success" type="button">check</button>--}}
    {!! $table !!}
</main>


</body>

<script>

    let td = document.getElementsByTagName('td');
    // for (let i = 0; i < q.length; i++) {
    //     if(q[i].innerHTML === '0' ) q[i].style.background = '#F0E68C';
    // }

    Array.prototype.forEach.call(td, function (item) {
        if (item.innerText === '0') item.style.background = '#F0E68C';
    });

    var codes = document.getElementsByClassName('code');
    Array.prototype.forEach.call(codes, function (item) {
        // console.log(item.getBoundingClientRect());
    });

    let qq = document.getElementById('scroll');
    if ( qq != null) {
        qq.onclick = function () {
            window.scroll({top: 1000, behavior: "smooth"});
        };
    }


</script>
</html>