$('[data-parrent-url]').each(function () {
    var parrent_url = new URL('http:%i/' + $(this).data('parrent-url')).pathname;
    $('.competition_navigation div').each(function () {
        var str = $(this).children('a').attr('href');
        var url = new URL('http://' + str);
        if (url.pathname.toLowerCase() === parrent_url.toLowerCase()) {
            $(this).addClass('parrent-select');
        }
    });
});

$('.competition_navigation div').click(function () {
    var str = $(this).children('a').attr('href');
    document.location = str;

});
