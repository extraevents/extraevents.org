<h3>
    <?= $data->title ?>
</h3>
<?= $data->table ?>
<?php if ($data->results_print_link ?? false) { ?>
    <a target='_blank' href='%i/<?= $data->results_print_link ?>'>
        <i class='far fa-file-alt'></i> Print
    </a>
<?php } ?>