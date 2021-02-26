<?php
page::set_title('Evolution');
?>
<div class='markdown-body'>
    <?= markdown::convertToHtml(file_get_contents(__DIR__ . '/evolution.md')); ?>
</div>