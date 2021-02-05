<?php

function tnoodle_hook_222pyra(&$rows) {
    foreach ($rows as &$row) {
        $row = str_replace(["R", "U", "F"], ["r", "u", "f"], $row);
        $row = str_replace(
                ["r2", "r'", "r", "u2", "u'", "u", "f2", "f'", "f"],
                ["R2", "R", "R'", "U2", "U", "U'", "B2", "B", "B'"],
                $row);
    }
}
