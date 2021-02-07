<?php
page::set_title('Evolution');
?>
<style>
<?php include_once('styles/external/markdown.css') ?>
</style>    
<div class='markdown-body'>
    <?= markdown::convertToHtml(file_get_contents(__DIR__ . '/evolution.md')); ?>
</div>