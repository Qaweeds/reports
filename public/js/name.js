$(document).ready(function () {
    $('.name-tr').each(function () {
        if(!$(this).next().hasClass('name-tr')) $(this).children('.name').css('background-color', 'rgba(255, 250, 205, .3)');
        else $(this).children('.name').css('background-color', 'rgba(152, 251, 152, .3)');
    });
    $('.magaz-name').css('background-color', 'rgba(255, 250, 205, .3)');

});