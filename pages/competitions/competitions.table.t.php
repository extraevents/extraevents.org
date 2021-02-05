<table class="grid competitions">
    <?php
    foreach ($data->competitions as $competition) {
        ?>
        <tr class="competition                     
        <?php foreach ($competition->events as $event) { ?>
            <?= $event->codeScript ?>
        <?php } ?>
            ">
            <td class="grid_center">
                <span class="status_icon <?= $competition->status ?>"></span>
            </td>            
            <td>            
                <b><?= $competition->date ?></b>    
            </td>   
            <td>
                <?= $competition->country->image ?>
            </td>
            <td>
                <a data-unnoficial="<?= $competition->unofficial ?>" href="<?= $competition->link ?>">
                    <span>
                        <?= $competition->name ?>
                    </span>
                </a>
            </td>
            <td>
                <span class="country">
                <?= $competition->country->name ?>
                </span>
                , <?= $competition->city ?>
            </td>
            <td class="events-image">
                <?php if (empty($competition->events)) { ?>
                    <i class="fas fa-ban"></i>
                <?php } else { ?>
                    <?php foreach ($competition->events as $event) { ?>
                        <?= $event->image ?>
                    <?php } ?>
                <?php } ?>    
            </td>
        </tr>
    <?php } ?>
</table>
<h2 ID='competitionsNotFound'>
    <i class="fas fa-exclamation-circle"></i>
    <?= t('search_results.not_found.competitions') ?>
</h2>