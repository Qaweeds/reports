$(document).ready(function () {
    $('.minus').parent().css('background-color', 'rgba(128, 128, 128, 0.35)');

    $('#hats-date').change(function () {
        $(this).closest('form').submit();
    });

    $('td').each(function () {
        if(this.innerHTML[0] === '-') $(this).css('background-color', 'rgba(128, 128, 128, 0.35)');
    });
});