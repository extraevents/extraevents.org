function select_card_id(card_id) {
    if (!card_id) {
        return;
    }
    var chosen = $('.chosen-select');
    var value = chosen.children('option[value=' + card_id + ']');

    chosen.val(value.val());
    chosen.trigger('chosen:updated.chosen');
    chosen.children('option').removeAttr('selected');
    value.attr('selected', 'selected');

    var row = $('tr[data-card_id = ' + card_id + ']');
    $('tr[data-card_id]').removeClass('result_competitor_select');
    row.addClass('result_competitor_select');
    var results = row.data('attempts');
    $('[data-enter-solve]').each(function () {
        if (results !== undefined) {
            var result = results[$(this).data('enter-solve')];
        } else {
            var result = 0;
        }
        $(this).val(result_format_enter(result));
        $(this).data('centisecond', result);
    });
    $('input[name=card_id]').val(card_id);
    $('.result_action button').show();
    check_results();
    $('.chosen-select').chosen('destroy').chosen();
    $('.chosen-select').
            trigger('chosen:updated.chosen');
    $('[data-enter-solve = 0]').focus();
    $('[data-result-clear]').attr('hidden', false);
}

$('.chosen-select').change(function () {
    select_card_id($(this).val());
});


$('tr[data-card_id]').click(function () {
    var card_id = $(this).data('card_id');
    select_card_id(card_id);

});

var round = $('[data-round]').data('round');

$('tr[data-card_id]').each(function () {
    $(this).addClass('results_row');
    var attempts = $(this).data('attempts');
    var single = $(this).data('single');
    var average = $(this).data('average');


    var result_max = Math.max.apply(Math, attempts);
    var result_min = Math.min.apply(Math, attempts);

    var solve_count = 0;
    for (var s in attempts) {
        if (attempts[s] !== 0) {
            solve_count++;
        }
    }
    for (var s in attempts) {
        var el = $(this).children('[data-tr-solve_' + s + ']');
        el.html(result_format_out(attempts[s]));
        if (round.format_solve_count == 5
                && solve_count == round.format_solve_count
                && attempts[s] === result_max) {
            result_max = null;
            el.addClass('attempt_except');
            continue;
        }

        if (round.format_solve_count == 5
                && solve_count == round.format_solve_count
                && attempts[s] === result_min) {
            result_min = null;
            el.addClass('attempt_except');
            continue;
        }
    }

    $(this).children('[data-tr-single]').
            html(result_format_out(single));
    $(this).children('[data-tr-average]').
            html(result_format_out(average));
    $(this).children('td[data-tr-single]').addClass('grid_bold');
    $(this).children('td[data-tr-average]').addClass('grid_bold');

});

function result_format_out(result) {
    if (result == -1) {
        return 'DNF';
    }

    if (result == -2) {
        return 'DNS';
    }

    if (result == 0 || result === undefined) {
        return '';
    }

    var minute = Math.floor(result / (100 * 60));
    result = result - minute * 100 * 60;
    var second = Math.floor(result / 100);
    result = result - second * 100;
    var centisecond = result;

    if (minute) {
        return minute + ':' + second + '.' + centisecond;
    }
    if (second) {
        if (centisecond < 10) {
            return second + '.0' + centisecond;
        } else {
            return second + '.' + centisecond;
        }
    }
    if (centisecond < 10) {
        return '0.0' + centisecond;
    }
    return '0.' + centisecond;

}

function result_format_enter(result) {

    if (result == -1) {
        return 'DNF';
    }
    if (result == -2) {
        return 'DNS';
    }

    var minute = Math.floor(result / (100 * 60));
    result = result - minute * 100 * 60;
    var second = Math.floor(result / 100);
    result = result - second * 100;
    var centisecond = result;

    var second_format = '00';
    var centisecond_format = '00';
    var minute_format = '';
    if (minute) {
        minute_format = minute + ':';
    }

    if (second && minute) {
        if (second < 10) {
            second_format = '0' + second;
        } else {
            second_format = second;
        }
    } else {
        second_format = second;
    }

    if (centisecond) {
        if (centisecond < 10) {
            centisecond_format = '0' + centisecond;
        } else {
            centisecond_format = centisecond;
        }
    }

    return  minute_format + second_format + '.' + centisecond_format;

}

$('[data-enter-solve]').on('input', function () {
    input_to_centisecond($(this));
    check_results();

});

function input_to_centisecond(el) {
    var value = el.val();
    if (value === '') {
        el.data('centisecond', 0);
    } else if (value.indexOf('f') !== -1 || value.indexOf('F') !== -1) {
        el.data('centisecond', -1);
    } else if (value.indexOf('s') !== -1 || value.indexOf('S') !== -1) {
        el.data('centisecond', -2);
    } else {
        value = value.replace(/\D+/g, '');
        value = value.substr(0, 6);
        value = '000000' + value;
        value = value.substr(value.length - 6);
        minute = Number.parseInt(value.substr(value.length - 6, 2));
        second = Number.parseInt(value.substr(value.length - 4, 2));
        centisecond = Number.parseInt(value.substr(value.length - 2));
        value = minute * 100 * 60 + second * 100 + centisecond;
        el.data('centisecond', value);
    }

    el.val(result_format_enter(el.data('centisecond')));
}


function check_results() {
    $('[data-enter-solve]').removeClass();
    $('[data-enter-solve]').attr('disabled', false);

    var round = $('[data-round]').data('round');
    var time_limit = +round.time_limit;
    var time_limit_cumulative = +round.time_limit_cumulative;
    var cutoff = +round.cutoff;
    var cutoff_is_set = cutoff > 0;
    var cutoff_solve_count = +round.format_cutoff_count;
    var cutoff_over = false;
    if (!cutoff_is_set) {
        cutoff_over = true;
    }
    var time_total = 0;
    var cumulative_over = false;
    var disabled_submit = false;
    var result_exists = false;

    $('#cutoff').removeClass('incorrect');
    $('#time_limit').removeClass('incorrect');

    $('[data-enter-solve]').each(function () {
        var attempt = +$(this).data('enter-solve');
        var centisecond = +$(this).data('centisecond')

        if (centisecond != 0) {
            result_exists = true;
        }

        time_total = time_total + centisecond;
        if (cutoff_is_set && attempt < cutoff_solve_count) {
            if (centisecond < cutoff && centisecond > 0) {
                cutoff_over = true;
            }
        }

        if (attempt >= cutoff_solve_count
                && !cutoff_over
                && centisecond != 0) {
            $(this).addClass('result_incorrect');
            $('#cutoff').addClass('incorrect');
            disabled_submit = true;
        }

        if (attempt >= cutoff_solve_count
                && !cutoff_over
                && centisecond == 0) {
            $(this).addClass('result_blank');
            $(this).attr('disabled', true);
        }

        if (cumulative_over) {
            $(this).addClass('result_blank');
            $(this).attr('disabled', true);
        }

        if (attempt < cutoff_solve_count
                && centisecond == 0) {
            $(this).addClass('result_incorrect');
            disabled_submit = true;
        }

        if (attempt >= cutoff_solve_count
                && cutoff_over
                && centisecond == 0) {
            $(this).addClass('result_incorrect');
            disabled_submit = true;
        }

        if ((!time_limit_cumulative && centisecond >= time_limit)
                || (time_limit_cumulative && time_total >= time_limit))
        {
            $(this).addClass('result_incorrect');
            $('#time_limit').addClass('incorrect');
            disabled_submit = true;
        }
        if (time_limit_cumulative && time_total >= time_limit) {
            cumulative_over = true;
        }
        if (time_limit_cumulative && centisecond < 0) {
            cumulative_over = true;
        }
    });

    if (!result_exists) {
        $('#cutoff').removeClass('incorrect');
        $('#time_limit').removeClass('incorrect');
        $('[data-enter-solve]').addClass('result_blank');
        disabled_submit = false;
    }


    $('[data-action=results] button').attr('disabled', disabled_submit);

}


$('[data-action=results]').keydown(function () {
    var key = event.which || event.keyCode;
    if (key === 13) {
        fn = function (elements, start) {
            for (var i = start; i < elements.length; i++) {
                var element = elements[i];
                if ((element.tagName === 'INPUT'
                        || element.tagName === 'BUTTON')
                        && element.disabled === false) {
                    element.focus();
                    break;
                }
            }
            return i;
        };
        var current = event.target || event.srcElement;

        for (var i = 0; i < this.elements.length; i++) {
            if (this.elements[i] === current) {
                break;
            }
        }
        if (fn(this.elements, i + 1) === this.elements.length) {
            fn(this.elements, 0);
        }
        if (current.tagName !== 'BUTTON') {
            return false;
        }
    }
});

$('[data-action=results]').submit(function () {
    var form = $(this);
    $('[data-enter-solve]').each(function () {
        var solve = $(this).data('enter-solve');
        var centisecond = $(this).data('centisecond');
        form.append('<input hidden name="attempt' + (solve + 1) + '" value="' + centisecond + '" />');
    });
});


$('.chosen-select').chosen();
$('.chosen-search-input').trigger('mousedown');

select_card_id($('[data-select_card_id]').data('select_card_id'));


$('[data-result-clear]').click(function () {
    $('[data-enter-solve]').each(function () {
        $(this).val('');
        input_to_centisecond($(this));
    });
    check_results();
    $('[data-enter-solve = 0]').focus();
    return false;
});