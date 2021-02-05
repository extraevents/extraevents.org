<h2>
    <?= t('competition.create') ?>
</h2>
<span class="error">
    <?= $data->settings_error ?>
</span>
<form data-action="create">
    <textarea name="settings" class="settings"><?= $data->settings ?></textarea>
    <br>
    <button>
        <i class="fas fa-plus-square"></i>
        <?= t('settings.create') ?>
    </button>
</form>
<style>
<?php include_once('styles/external/markdown.css') ?>
</style>    
<div class='markdown-body'>
    <?= $data->markdown_competition ?>    
</div>