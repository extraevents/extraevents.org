
addEventListener("keydown", function (e) {
    switch (e.keyCode) {
        case 32:
            e.preventDefault();
            location.reload();
            break;
    }
});


$('.training_scramble textarea').click(function () {
    $(this).focus();
    $(this).select();
});


$('[data-navigation-event]').each(function () {
    var url = new URL(location.href);
    let result = url.href.match(/events\/(.*)\/training/);
    console.dir(result);
    $(this).val(result[1]);
});


$('[data-navigation-event]').change(function () {
    var url = new URL(location.href);
    var value = $(this).val();
    location.href = url.href.replace(/events\/.*\/training/g, "events/" + value + "/training");
});
