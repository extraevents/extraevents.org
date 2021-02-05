
<div class='competition_navigation'>
    <div>
        <?= $data->competition_line ?>
    </div>
    <?php foreach ($data->navigation as $navigation) { ?>
        <div class='section' >    
            <a href="<?= $navigation->url ?>"><?= $navigation->icon ?> <?= $navigation->title ?></a>   
        </div>
    <?php } ?>
</div>
<p class='competition-comment'>
    <i class="far fa-comment-alt"></i>
    <?= t("competition.statuses_extended." . $data->status); ?> 
</p>