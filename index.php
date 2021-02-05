<?php
include_once 'autoload.php';
ob_start();
?>
<!DOCTYPE HTML>
<html lang='en'>
    <head>
        <meta name='Description' content='<?= config::get()->title ?>'>
        <meta charset='utf-8'>
        <title>{%title}</title>
        <?php page::include('index.head'); ?>
    </head>
    <body id='variables' data-index='%i/'>
        <?php page::include('body'); ?>
    </body>
</html> 

<script src="%i/index.js"></script>
<?php
db::close();
$ob = ob_get_contents();
ob_clean();
$ob1 = str_replace(
        ['{%title}', "%i/"],
        [page::get_title(), PageIndex() . "/"],
        $ob);
echo $ob1;
