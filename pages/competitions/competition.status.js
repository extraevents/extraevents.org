$('form[data-available_action] button').click(function () {
    $(this).parent().find('input[name=description]').val(
            $('textarea[data-description]').val()
            );
});