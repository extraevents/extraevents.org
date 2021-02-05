$('select[data-selected]').each(function () {
    var selected = $(this).data('selected');
    $(this).find('option[value="' + selected + '"]').prop('selected', true);
});

$('[data-block-name]').each(function () {
    var name = $(this).data('block-name');
    ;
    $(this).appendTo('[data-block-from=' + name + ']');
    $(this).show();
});

$('[data-confirm-delete]').submit(function () {
    return confirm('Attention: Confirm the deletion.');
});

$('[data-confirm]').submit(function () {
    return confirm('Confirm: ' + ($(this).find('button').text()).trim());
});

$('button[data-href]').click(function () {
    document.location.href = $(this).data('href');
});

$('[data-unnoficial=1]').addClass('unofficial');

$('[data-location]').html(document.location.href);

$('[data-hidden = 1]').hide();

$('[data-selected = 1]').addClass('selected');

$('[data-selected-value]').each(function () {
    var value = $(this).data('selected-value');
    $(this).find('[data-selected-condition=' + value + ']').addClass('selected');

});

$('[data-hidden-href-empty]').each(function () {
    if ($(this).find('a').attr('href') === '') {
        $(this).hide();
    }
});

$('a[data-external-link]').each(function () {
    var url = $(this).data('external-link');
    $(this).attr('target', '_blank');
    $(this).addClass('external-link');
    if (url) {
        $(this).attr('href', url);
    }
});

$('[data-attempt-except]').each(function () {
    var html = $(this).html().trim();
    if ($(this).data('attempt-except') === 1) {
        $(this).html('(' + html + ')');
    } else {
        $(this).html("\u00A0" + html + "\u00A0");
    }
});


var date = new Date();
var options = {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    timezone: 'UTC',
    timeZoneName: 'short',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hour12: false
};
var date_str = date.toLocaleString("en-US", options);

$('a[data-add-date]').click(function () {
    $(this).attr('href', $(this).attr('href') + '/?date=' + date_str);
});

$('form[data-add-date]').submit(function () {
    $(this).append('<input hidden value="' + date_str + '" name="date">');
});

$('input[data-add-date]').each(function () {
    $(this).attr('name', 'date');
    $(this).attr('value', date_str);
});


$('a[data-form-post]').click(function () {
    var action = $(this).data('form-post');
    $(this).append("<form hidden method='POST' action='" + action + "'></form>");
    $(this).find('form').submit();
});

$('form').submit(function () {
    window.location.hash = ' ';
    $(this).attr('method', 'POST');
    var action = $(this).data('action');
    if (action) {
        $(this).append('<input hidden value="' + action + '" name="action">');
    }
});

$('[data-utc-time]').each(function () {
    var options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        hour12: false,
        minute: 'numeric',
        timeZoneName: 'short'};
    var datetime = $(this).data('utc-time');
    if (datetime) {
        var date = new Date(datetime);
        $(this).html(date.toLocaleDateString("en-US", options));
    }
});

$('button[data-value]').each(function () {
    var value = $(this).data('value');
    $(this).html(value);
    $(this).attr('value', value);
});

$('button[data-link]').click(function () {
    var link = $(this).data('link');
    location.href = link;

});

$('input[data-filter]').each(function () {
    $(this).addClass('filter');
});

$('input[data-filter]').keyup(function () {
    var val = $(this).val().toLowerCase();
    var i = 0;
    var fields = $(this).data('filter');
    var id = $(this).data('table-id');
    $('table[data-table-id=' + id + '] tbody tr').each(function () {
        $(this).removeClass('odd');
        $(this).removeClass('even');
        var show = false;
        var tr = $(this);
        fields.forEach(function (field) {
            var value = tr.children('[data-tr-' + field + ']').text().toLowerCase();
            if (value.indexOf(val) + 1) {
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
    });

});


$('tr[data-except]').each(function () {
    var except = $(this).data('except');
    var tr = $(this);
    except.forEach(function (attempt) {
        tr.children('[data-tr-attempt_' + (attempt + 1) + ']').addClass('attempt_except');
    });

});


$('textarea.settings').on('input', function () {
    var val = $(this).val();
    $(this).removeClass('json_invalid');
    try {
        var c = $.parseJSON(val);
    } catch (err) {
        $(this).addClass('json_invalid');
    }
});
$('textarea.settings').trigger("input");

