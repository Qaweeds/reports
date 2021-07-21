$(document).ready(function () {


    /*CLOSE IMAGE BOX */
    $('#box').click(function () {
        if ($(window).width() < 576) $('.city-table').css('margin-top', '0');
        $('#box').empty();
        $('.distributor-full-name').empty();
    });



    /* HEADER MENU DROPDOWN */
    $('.dropdown').click(function () {
        $('.dropdown-menu').toggle();
    });


    /* YELLOW-LINE  ONCLICK TABLE FUNCTION*/
    $('tbody tr').click(function (event) {
        var q = event.target;
        if ($(q).hasClass('name')) {
            if ($(window).width() < 756) $('.city-table').css('margin-top', '213px');
            $text = names[q.innerText][0] + "<br>" + names[q.innerText][1];
            $src = names[q.innerText][0].replace(/\s+/g, ''); // Удалить пробелы
            $img = '<img   onerror="imgError(this);" style="height: 250px;" src="storage/Реализаторы/' + $src + '.jpg">';
            $('.distributor-full-name').empty().html($text);
            $('#box').empty().append($img);
            $('.my-table').find('.yellow-line').removeClass('yellow-line');
            $(this).addClass('yellow-line');
            $(this).children('.name').addClass('yellow-line');
            $(this).children('.main-store').addClass('yellow-line');
            $(this).children('.places-cell').addClass('yellow-line');
            return false;
        } else {
            if ($(window).width() < 756) $('.city-table').css('margin-top', '0');
            $('.distributor-full-name').empty();
            $('#box').empty();
            if ($(this).hasClass('yellow-line')) {
                $(this).removeClass('yellow-line');
                $(this).children('.name, .main-store, .places-cell').removeClass('yellow-line');
            } else {
                $('.my-table').find('.yellow-line').removeClass('yellow-line');
                $(this).addClass('yellow-line');
                $(this).children('.name, .main-store, .places-cell').addClass('yellow-line');
            }
        }
    });


    $(window).resize(function () {
        if ($('#box').children().length > 0) {
            if ($(window).width() < 576) $('.city-table').css('margin-top', '213px');
        } else {
            if ($(window).width() < 576) $('.city-table').css('margin-top', '0');
        }
    });

    $('.date-form input').focus(function () {
        this.type = 'date';
        this.setAttribute('min', '2021-03-01');
    }).blur(function () {
        if (!this.value) this.type = 'text';
    });

    $('.date-form button').click(function () {

        var d = new Date();
        var month = d.getMonth() + 1;
        var day = d.getDate();

        $today = d.getFullYear() + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
        $date = $('#dateStart').val();
        if ($date > $today) {
            event.preventDefault();
            $('#wrong-date-alert').html('Начальная дата не может быть больше, чем сегодняшняя дата!').css('display', 'block');
        }

        if ($date < '2021-03-01') {
            event.preventDefault();
            $('#wrong-date-alert').html('Укажите дату больше, чем  &laquo;1 марта 2021 года&raquo;!').css('display', 'block');
        }
    });

});


/* replacer if photo not found */
function imgError(image) {
    image.onerror = "";
    image.src = "storage/Реализаторы/no.png";
    return true;
}

function printpage() {
    window.print()
}

/* SORT TABLE FUNCTION
*
* found on stackoverflow
*
* */
document.addEventListener('DOMContentLoaded', () => {
    var getSort = ({target}) => {
        if(target.textContent === 'M' || target.textContent === 'ФИО'){
            var order = (target.dataset.order = 1);
        }else{
            order = (target.dataset.order = -1);
        }
        var index = [...target.parentNode.cells].indexOf(target);
        var collator = new Intl.Collator(['en', 'ru'], {numeric: true});
        var comparator = (index, order) => (a, b) => order * collator.compare(
            a.children[index].innerHTML,
            b.children[index].innerHTML
        );

        for (var tBody of target.closest('table').tBodies)
            tBody.append(...[...tBody.rows].sort(comparator(index, order)));

        for (var cell of target.parentNode.cells)
            cell.classList.toggle('sorted', cell === target);
    };

    document.querySelectorAll('.sort').forEach(tableTH => tableTH.addEventListener('click', () => getSort(event)));

    $('.sort').click(function () {
        $('.places-cell').each(function (i, elem) {
            if (i) $(elem).html(i);
        });
    })
});
