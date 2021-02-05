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