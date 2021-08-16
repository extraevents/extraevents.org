$('[data-filter]').keyup(function () {
    $('[data-navigation-event]').prop('selectedIndex', 0);
});
$('select[data-navigation-event]').change(function () {
    $('[data-filter]').val('');
    var event_id = $(this).val();
    var i = 0;
    $('table tbody tr').each(function () {
        $(this).removeClass('odd');
        $(this).removeClass('even');
        var tr = $(this);
        var show = false;
        tr.find('td[data-tr-events] i').each(function () {
            if ($(this).data('id') === event_id || event_id === 'all') {
                show = true;
            }
        });

        if (show) {
            if (i % 2 === 0) {
                $(this).addClass('odd');
            } else {
                $(this).addClass('even');
            }
            i = i + 1;
            $(this).show();
        } else {
            $(this).hide();
        }
    }
    );
});
