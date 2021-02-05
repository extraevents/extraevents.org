
<div class='navigator' data-selected='<?= $data->section ?>'>
    <div class="logo">
        <a href='%i/'>
            <img title='<?= $data->title ?>' src="%i/logos/color.svg">
        </a>
    </div>
    <?php foreach ($data->navigations as $link => $navigation) { ?>
        <div class="section" data-section="<?= $link ?>">
            <a>
                <i class="<?= $navigation->icon ?>"></i>
                <?= t($navigation->title) ?>
            </a>
        </div>
    <?php } ?>
    <div class='competitor' data-block-from='body_competitor_panel'>
    </div>
</div>