<?php

class region {

    static function filter() {
        ob_start();
        ?>
        <i class="fas fa-filter"></i>
        <select class="chosen-select">
            <option value=''><?= t('region_filter.world') ?></option>
            <optgroup label='<?= t('region_filter.continent') ?>'> 
                    <?php foreach (db::rows("SELECT id, name FROM continents WHERE record_name<>'' ORDER BY name") as $continent) { ?>
                    <option value='?region=<?= $continent->id ?>'>
                    <?= $continent->name ?>
                    </option>
        <?php } ?>
            <optgroup/>    
            <optgroup label='<?= t('region_filter.country') ?>'> 
                    <?php foreach (db::rows("SELECT id, name FROM countries WHERE length(id)>2 ORDER BY name") as $region => $country) { ?>
                    <option value='?region=<?= $country->id ?>'>
                    <?= $country->name ?>
                    </option>
        <?php } ?>
            <optgroup/>    
        </select>
        <?php
        $content = ob_get_contents();
        ob_end_clean();
        return
                $content;
    }
    
    static function flag($country_name, $country_iso2) {
        $lower_iso2 = strtolower($country_iso2);
        return
                "<i title='$country_name' class='flag-icon flag-icon-$lower_iso2'></i>";
    }

}
