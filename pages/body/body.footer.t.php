<div class='footer'>
    <a href="mailto:<?= $data->contacts->leaders->mail ?>?subject=<?= $data->contacts->leaders->subject ?>">
        <i class="far fa-envelope"></i>
        <?= t('contact.leaders') ?>
    </a>
    <a href="mailto:<?= $data->contacts->support->mail ?>?subject=<?= $data->contacts->support->subject ?>">
        <i class="far fa-envelope"></i>
        <?= t('contact.support') ?>
    </a>
    <a href="%i/icons">
        <i class="fas fa-image"></i>
        <?= t('navigation.icons') ?>
    </a>
    <a target="_blank" href="https://github.com/extraevents/extraevents.org">
        <i class="fab fa-github"></i>
        GitHub
    </a>
    <a href="%i/export">
        <i class="fas fa-download"></i>
        <?= t('navigation.export_db') ?>
    </a>

    <a href="%i/api">
        <i class="fas fa-code"></i>
        <?= t('navigation.api') ?>
    </a>


    <a target="_blank"  href="<?= $data->owner->url ?>">
        <i class="fas fa-laptop-code"></i>
        <?= $data->owner->name ?> 
        <i class="flag-icon flag-icon-ru"></i>
    </a>

</div>    