<?php
$data = filter_input(INPUT_GET, 'data');
$allowed = filter_input(INPUT_GET, 'allowed');
$filename = filter_input(INPUT_GET, 'filename');
$php_self = filter_input(INPUT_SERVER, 'PHP_SELF');
$http_host = filter_input(INPUT_SERVER, 'HTTP_HOST');
$json = json_decode($data);
?>
<script src='../scripts/external/jquery-3.4.1.min.js' type='text/javascript'></script>
<script>
    var data = '<?= $data ?>';
    var filename = '<?= $filename ?>';

    function get_version(data, key_version) {
        if ($('#version').html() !== '') {
            return;
        }
        $.each(data, function (key, val) {
            if (key === key_version) {
                $('#version').html(val);
            }
        });

        var current = $('#version').html();
        var error = true;
        $.map($.parseJSON('<?= $allowed ?>'),
                function (value, key) {
                    if (current != '' && value.indexOf(current) != -1) {
                        error = false;
                    }
                });

        $.map($.parseJSON('<?= $allowed ?>'),
                function (value, key) {
                    if (current != '' && value.indexOf(current) != -1) {
                        downoad();
                        return;
                    }
                });

        if (error) {
            $('#status').html(
                    'Error! ' +
                    '<br>Allowed versions: <?= $allowed ?>' +
                    '<br>Runnig version: ' + current
                    );
        }
    }

    function check_tnoodle() {
        $.getJSON('http://localhost:2014/version',
                function (data) {
                    get_version(data, 'runningVersion');
                    get_version(data, 'projectVersion');
                })
                .fail(function () {
                    $.getJSON('http://localhost:2014/version.json',
                            function (data) {
                                get_version(data, 'running_version');
                            }).fail(function () {
                        $('#status').html('Error!');
                        $('#manual').show();
                    });
                });
    }

    function downoad() {
        $('#status').html('loading...');
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            var a;
            if (xhttp.readyState === 4 && xhttp.status === 200) {
                a = document.createElement('a');
                a.href = window.URL.createObjectURL(xhttp.response);
                a.download = filename + ".zip";
                a.style.display = 'none';
                document.body.appendChild(a);
                a.click();
                $('#status').html('[' + filename + ']<br>download completed');
                b = document.createElement('a');
                b.innerHTML = 'close';
                b.href = '#';
                b.addEventListener("click",
                        function () {
                            window.close();
                        });
                document.body.appendChild(b);
            }
            if (xhttp.status === 400 || xhttp.status === 404) {
                $('#status').html(xhttp.statusText);
            }
        };
        xhttp.open("POST", 'http://localhost:2014/wcif/zip');
        xhttp.setRequestHeader("Content-Type", "application/json");
        xhttp.responseType = 'blob';
        xhttp.send(data);
    }
    ;
    check_tnoodle();
</script>
<title><?= $json->wcif->name ?></title>
<p>
    <span id="version" style="display: none"></span>
    <span id="status">Wait for the Tnoodle check...</span>
</p>
<div id="manual">
    <font color="red">Tnoodle not runnig</font><br>
    Or problems with CORS in your browser (try Safari).<br>
    <hr>
    You can save this page and open it.<br>
    <?php
    $events = [
        '333' => '3x3x3 Cube',
        '222' => '2x2x2 Cube',
        '444' => '4x4x4 Cube',
        '555' => '5x5x5 Cube',
        '333oh' => '3x3x3 One-Handed',
        'pyram' => 'Pyraminx',
        'skewb' => 'Skewb',
        'clock' => 'Clock',
        'sq1' => 'Square-1',
        'minx' => 'Megaminx',
        '333bf' => '3x3x3 Blindfolded',
        '666' => '6x6x6 Cube',
        '777' => '7x7x7 Cube',
        '333fm' => '3x3x3 Fewest Moves',
    ];
    $formats = [
        'm' => 'Mo3',
        'a' => 'Ao5',
    ];
    ?>
    <hr>
    You can generate scrambles manually in <a target='_blank' href="http://localhost:2014/">Toddler</a> ( <?= $allowed ?>)<br>
    <b>Competition Name</b>: <?= $json->wcif->name ?><br>
    <?php
    foreach ($json->wcif->events as $event) {
        $round = $event->rounds[0];
        ?>
        <b>Event</b>: <?= $events[$event->id] ?><br>
        <b>Rounds</b>: 1<br>
        <b>Format</b>: <?= $formats[$round->format] ?><br>
        <b>Scramble Sets</b>: <?= $round->scrambleSetCount ?><br>
        <b>Copies</b>: 1<br>
    <?php } ?>
</div>
<script>
    $('#manual').css('display', 'none');
</script>
<?php
?>    