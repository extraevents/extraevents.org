<h1>
    <?= t('navigation.icons') ?>
</h1>
<h3>
    JPG
</h3>
<a data-image-link='<?= PageIndex() ?>/logos/color.jpg'></a>
<a data-image-link='<?= PageIndex() ?>/logos/black.jpg'></a>
<h3>
    PNG
</h3>
<a data-image-link='<?= PageIndex() ?>/logos/color.png'></a>
<a data-image-link='<?= PageIndex() ?>/logos/black.png'></a>
<h3>
    SVG
</h3>
<a data-image-link='<?= PageIndex() ?>/logos/color.svg'></a>
<a data-image-link='<?= PageIndex() ?>/logos/black.svg'></a>
<h1>
    Events
</h1>
<h3>
    SVG
</h3>
<?php foreach ($data->filenames as $filename) { ?>
    <a data-image-link='<?= PageIndex() ?>/svgs/<?= $filename ?>'></a>
<?php } ?>