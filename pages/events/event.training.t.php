<h1>
    <?= t('training.title') ?>
    <?= $data->event->line() ?>
</h1>
<?= event::filter("scramble_training<>''") ?>
<div><?= $data->comments; ?></div>
<div class='training_scramble'><?= $data->scramble; ?></div>
<div class='training_image'>
    <img src="%i/<?= $data->filename ?>?t=<?= time() ?>"/>
</div>   
<p>
    <i class="fas fa-keyboard"></i>
    <?= t('training.instruction') ?>
</p>
    <form data-action="generate" data-add-date target="_blank">

        <i class="fas fa-random"></i> 
        <?= t('training.generate_pdf') ?>
        <input hidden name='event' value='<?= $data->event->id ?>'/>
        <button name='set_count' data-value='2'></button>
        <button name='set_count' data-value='3'></button>
        <button name='set_count' data-value='4'></button>
        <button name='set_count' data-value='5'></button>
    </form>   
