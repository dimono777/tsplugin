<?php

use tradersoft\components\redirect_ip_country\model\Settings as RedirectIpCountrySettings;

?>

<div class="wp-tradersoft-title">
    <?php echo TS_Functions::__(RedirectIpCountrySettings::getBlockTitle()); ?>
</div>
<div class="form-field m-b-30">
    <label class="small-label"><?php echo TS_Functions::__(RedirectIpCountrySettings::getRuleTitle()); ?></label>
    <div class="checkbox-switcher">
        <input  id="el_<?php echo TS_Functions::__(RedirectIpCountrySettings::getRuleName()); ?>"
                type="checkbox"
                name="<?php echo TS_Functions::__(RedirectIpCountrySettings::getRuleName()); ?>"
                onchange="this.value = +this.checked"
                <?php echo $current_redirect_ip_country ? "checked" : ''; ?>

        />
        <div class="switch-title"></div>
        <label for="el_<?php echo TS_Functions::__(RedirectIpCountrySettings::getRuleName()); ?>" class="switch-selection"></label>
    </div>
</div>

<!--<div class="form-field">-->
<!--    <label>--><?php //echo \TS_Functions::__(RedirectIpCountrySettings::getRuleTitle()); ?><!--</label>-->
<!--    <div class="select-wrapper">-->
<!--        <select name="--><?php //echo \TS_Functions::__(RedirectIpCountrySettings::getRuleName()); ?><!--">-->
<!--            --><?php //foreach (RedirectIpCountrySettings::getRedirectRuleOptions() as $optionValue => $optionTitle): ?>
<!--                <option value='--><?php //echo $optionValue; ?><!--'-->
<!--                    --><?php //echo ($optionValue == $current_redirect_ip_country) ? "selected = 'selected'" : ''; ?>
<!--                >-->
<!--                    --><?php //echo \TS_Functions::__($optionTitle); ?>
<!--                </option>-->
<!--            --><?php //endforeach; ?>
<!--        </select>-->
<!--    </div>-->
<!--</div>-->