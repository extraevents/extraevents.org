$('td[data-tr-result]').addClass('grid_bold');

$('.chosen-select').chosen();

$('.chosen-select').change(function () {
    var url = new URL(location.href);
    url.search = $(this).val();
    location.href = url.toString();
});


$('.chosen-select').each(function () {
    var url = new URL(location.href);
    var value = url.search;
    $(this).val(decodeURI(value));
    $('.chosen-select').
            trigger('chosen:updated.chosen');
});


$('[data-navigation-event]').each(function () {
    var url = new URL(location.href);
    let result = url.href.match(/events\/(.*)\/rankings/);
    console.dir(result);
    $(this).val(result[1]);
});


$('[data-navigation-event]').change(function () {
    var url = new URL(location.href);
    var value = $(this).val();
    location.href = url.href.replace(/events\/.*\/rankings/g, "events/" + value + "/rankings");
});


$('.navigation2 div.section').each(function () {
    var str = $(this).children('a').attr('href');
    var url = new URL('http://' + str);
    var pathname = document.location.pathname;
    if (pathname===url.pathname) {
        $(this).addClass('select');
    }
});


$('.navigation2 div.section').click(function () {
   location.href = $(this).children('a').attr('href'); 
});


