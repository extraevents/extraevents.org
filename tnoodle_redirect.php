<?php
$data = filter_input(INPUT_GET, 'data');
$allowed = filter_input(INPUT_GET, 'allowed');
$filename = filter_input(INPUT_GET, 'filename');
$php_self = filter_input(INPUT_SERVER, 'PHP_SELF');
$http_host = filter_input(INPUT_SERVER, 'HTTP_HOST');
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
                        $('#status').html('Error!<br>Tnoodle not runnig<br>Or you have Access-Control-Allow-Origin enabled in the browser.');
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
<p>
    <span id="version" style="display: none"></span>
    <span id="status">Wait for the Tnoodle check...</span>
</p>