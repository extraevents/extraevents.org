$('.status_icon.covid-19').addClass('fas fa-clinic-medical color_red');
$('.status_icon.hidden').addClass('fas fa-eye-slash color_orange');
$('.status_icon.upcoming').addClass('fas fa-hourglass-start color_light_gray');
$('.status_icon.running').addClass('fas fa-hourglass-half color_green');
$('.status_icon.past').addClass('fas fa-hourglass-end');
$('.status_icon.technical').addClass('fas fa-cogs color_blue');

if ($('.competition').length > 0) {
    $('#competitionsNotFound').hide();
}