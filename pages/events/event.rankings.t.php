<h1>
    <?= t('rankings.title') ?>
    <?= $data->title ?>
</h2>
<?= event::filter() ?>
<div class='navigation2'>
    <?php foreach ($data->navigation as $navigation) { ?>
        <div class='section' >    
            <a href="<?= $navigation->url ?>?<?= query::full() ?>">
                <?= $navigation->icon ?> <?= $navigation->title ?>
            </a>   
        </div>
    <?php } ?>
</div>

<?= region::filter() ?>
<?= $data->table ?>
