$('#competitor_panel').on('click', function () {
    if ($(this).hasClass('competitor_panel_link')) {
        $('.competitor-panel').show();
        $(this).addClass('competitor_panel_open_link');
        $(this).removeClass('competitor_panel_link');
    } else {
        $('.competitor-panel').hide();
        $(this).addClass('competitor_panel_link');
        $(this).removeClass('competitor_panel_open_link');
    }
    return false;
});

$('.language_set select').on('change', function () {
    $(this).closest('form').submit();
});