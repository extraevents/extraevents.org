$('.navigator div.section').each(function () {
    $(this).mouseover(function () {
        $(this).addClass('hover');
    });

    $(this).mouseout(function () {
        $(this).removeClass('hover');
    });

    $(this).each(function () {
        var href = '%i/' + $(this).data('section');
        $(this).find('a').attr('href', href);
        $(this).on("click", function () {
            document.location.href = href;
        });
    });
});

var selected = $('.navigator').data('selected');
$('.navigator div[data-section="' + selected + '"]').addClass("select");