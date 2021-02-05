$('.competition_navigation div.section').each(function () {
    var str = $(this).children('a').attr('href');
    var url = new URL('http://' + str);
    var pathname = document.location.pathname;
    if (pathname===url.pathname) {
        $(this).addClass('select');
    }
});

