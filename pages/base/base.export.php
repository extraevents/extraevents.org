<?php

page::set_title(t('navigation.export_db'));
$export = new build_table();
$export->add_head('format', false);
$export->add_head('file', 'File');
$export->add_head('time', 'Time');
$export->add_head('size', 'Size');

foreach (['sql', 'tsv'] as $format) {
    $export_file = backup::last('export', 'public', $format);
    $export_file->format = $format;
    if ($export_file->exists) {
        $row = new build_row();
        $row->add_value('format', "<span data-export-icon='$format'></span> $format");
        $row->add_value('file', '<a href="' . $export_file->path . '">' .
                "<i class='fas fa-download'></i> " .
                $export_file->basename . '</a>');
        $row->add_value('time', '<span data-utc-time="' . $export_file->timestamp . '" />');
        $row->add_value('size', round($export_file->get_size()) . ' KB');
        $export->add_tr($row);
    }
}

$data = arrayToObject([
    'export' => $export->out(),
        ]);

