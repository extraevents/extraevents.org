<style>
<?php include_once('styles/external/markdown.css') ?>
</style>    
<h1><?= t('navigation.team_import') ?></h1>

<h3>Loading file with team
    <a target='_blank' href='%i/import/team_schema.json'>json-schema</a>
</h3>
<form enctype="multipart/form-data" data-action='team.import'>
    <input required type="file" name="file">
    <input type="submit" value="Load">
</form>
<?= $data->message ?>
<hr>
<div class='markdown-body'>
    <?= $data->markdown_team ?>    
</div>