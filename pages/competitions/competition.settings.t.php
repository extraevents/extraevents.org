<h2>
    <?= t('competition.settings') ?>
</h2>
<span class="error">
    <?= $data->settings_error ?>
</span>
<form data-action="save">
    <textarea name="settings" class="settings"><?= $data->settings ?></textarea>
    <br>
    <button>
        <i class="fas fa-save"></i>
        <?= t('settings.save') ?>
    </button>
</form>
<style>
<?php include_once('styles/external/markdown.css') ?>
</style>    
<div class='markdown-body'>
    <?= $data->markdown_competition ?>    
</div>