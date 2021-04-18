<h1>
    <?= t('navigation.support_login_share') ?>
</h1>
<?php
$session = request::get(2);
$user = wcaoauth::get_user_by_session($session);
if (!$user) {
    ?>
    <span class="error">Wrong share_link</span>
    <?php
} else {
    wcaoauth::set_session($session);
    ?>
    <span class="message">You are signed in as <?= "$user->name [$user->wca_id]" ?></span>
<?php } ?>