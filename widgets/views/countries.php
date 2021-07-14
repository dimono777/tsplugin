<select id="countries_list" name="country" class="olgs_input_select_country" data-default="<?php echo ($currentCountryCode = TSInit::$app->trader->getCountryCode()); ?>">
    <option value=""><?php echo \TS_Functions::__('Select country'); ?></option>
    <?php

        foreach (\tradersoft\helpers\Interlayer_Crm::getCountriesAll() as $code => $name) {
            ?><option value="<?php echo $code; ?>"<?php echo ($currentCountryCode == $code) ? ' selected="selected"' : ''; ?>><?= $name ?></option><?php
        }

    ?>
</select>