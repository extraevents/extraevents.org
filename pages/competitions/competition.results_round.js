
$('tr[data-next_mark=top]').each(function () {
    $(this).children('td[data-tr-mark]').addClass('result_top');
});

$('tr[data-next_mark=next]').each(function () {
    $(this).children('td[data-tr-mark]').addClass('result_next');
});

$('tr[data-next_mark!=""]').each(function () {
    $(this).children('td[data-tr-competitor]').addClass('competitor_mark');
});


$('tr[data-except]').each(function () {
    var except = $(this).data('except');
    var tr = $(this);
    except.forEach(function (item) {
        var attempt=item+1;
        tr.children('td[data-tr-attempt_' + attempt + ']').addClass('attempt_except');
    });
});