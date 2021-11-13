<?php
session_start();
require 'autoload.php';
new autoload();
ob_start();
?>
<!DOCTYPE HTML>
<html lang='en'>
    <head>
        <meta charset='utf-8'>
        <title>%title</title>
        <link rel="icon" href="%i/logos/color.png" >

        <link rel="stylesheet" href="%i/styles/core/style.css" type="text/css"/>
        <link rel="stylesheet" href="%i/styles/core/icons-extra-event/css/Extra-Events.css" type="text/css"/>    
        <link rel="stylesheet" href="%i/styles/external/fontawesome-free-5.15.1-web/css/all.css" type="text/css"/>
        <link rel="stylesheet" href="%i/styles/external/flag-icon-css/css/flag-icon.css" type="text/css"/>
        <link rel="stylesheet" href="%i/styles/external/markdown.css" type="text/css"/>

        <script src="%i/scripts/external/jquery-3.4.1.min.js" type="text/javascript"></script>

        <link rel="stylesheet" href="%i/scripts/external/chosen_v1/chosen.css" type="text/css"/>
        <script src="%i/scripts/external/chosen_v1/chosen.jquery.js" type="text/javascript"></script> 
    </head>
    <body>        
        <?php page::include('body'); ?>
    </body>
</html> 

<script src="%i/index.js"></script>
<?= page::push() ?>
<?= db::close() ?> 