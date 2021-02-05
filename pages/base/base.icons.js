$('[data-image-link]').each(function () {
    var link = $(this).data('image-link');
    $(this).append('<img>');
    $(this).attr('href', link);

    var img = $(this).find('img');
    img.attr('src', link);
    img.attr('title', link.split('/').pop().split('.')[0]);
    img.addClass('icons');
});