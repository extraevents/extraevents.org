$('form[data-action="register"]').submit(function () {
    var key = $(this).closest('tr').find('input[data-team-key]');
    if (key.length) {
        $(this).append('<input hidden value="' + key.val() + '" name="team-key">');
    }
});


$('form[data-event]').submit(function () {
    var event = $(this).data('event');
    $(this).append('<input hidden value="' + event + '" name="event">');
});