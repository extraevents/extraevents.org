<div hidden data-block-name='body_competitor_panel'>    
    <?php if ($data->user) { ?>
        <a href="#" id="competitor_panel" class="competitor_panel_link">
            <i class="fas fa-user"></i>    
            <?= $data->user->name ?>
        </a>
    <?php } else { ?>
        <a href="<?= wcaoauth::url() ?>">
            <i class="fas fa-sign-in-alt"></i> 
            <?= t('aouth.login') ?>
        </a>
    <?php } ?>    
</div>

<div class="competitor-panel">
    <?php if ($data->person) { ?>
        <?php foreach ($data->links as $link) { ?>
            <a href="<?= $link->link ?>"><?= $link->value ?></a>
        <?php } ?>

        <?php if($data->person_link){ ?>
        <a href="<?= $data->person_link ?>">
            <i class="fas fa-user"></i>
            <?= t('navigation.my_results') ?>
        </a>
        <?php } ?>
            <?php if($data->person_link){ ?>
        <a href="%i/competitions/mine">
            <i class="fas fa-cube"></i>
            <?= t('navigation.my_competitions') ?>
        </a>
        <?php } ?>
        <a href="#" data-form-post='%i/persons/logout'>
            <i class="fas fa-sign-out-alt"></i> 
            <?= t('aouth.logout') ?>
        </a>
        <a href="#" data-form-post='<?= PageIndex() ?>/persons/logout_all'>
            <span class="color_red">
                <i class="fas fa-sign-out-alt"></i> 
                <?= t('aouth.logout_all') ?> (<?= $data->person->auth_count ?>)
            </span>
        </a>
    <?php } ?>

</div>     