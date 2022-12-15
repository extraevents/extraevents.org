<div class="wrapper_content">
    <?php page::include('body.navigator', $data); ?>
    <?php if (!(config::get()->freeze ?? false)) page::include('body.person', $data); ?>
    <?php page::include('body.message', $data); ?>
    <?php page::include_main(); ?>
</div>    
<footer class="footer_content">
    <?php page::include('body.footer', $data); ?>
</footer>

