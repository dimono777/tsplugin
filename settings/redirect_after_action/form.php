<?php

use tradersoft\settings\redirect_after_action\Model as RedirectAfterActionModel;
use tradersoft\model\redirect_after_action\projects\Site as ProjectSite;
use tradersoft\model\redirect_after_action\projects\Platform as ProjectPlatform;
use tradersoft\model\redirect_after_action\projects\Custom as ProjectCustom;

$redirectAfterActionModel = new RedirectAfterActionModel();

?>

<link rel="stylesheet" href="<?php echo \tradersoft\helpers\Assets::findUrl('/css/redirect_after_action.css', 'admin'); ?>?v=1">
<?php foreach ($redirectAfterActionModel->getActions() as $action) { /** @var tradersoft\model\redirect_after_action\abstracts\Actions $action */ ?>
    <div class="wp-tradersoft-title">
        <?php echo \TS_Functions::__('Redirect after ' . $action::NAME); ?>
    </div>
    <div class="form-field">
        <?php foreach ($redirectAfterActionModel->getCombinationsOfRules($action->getRules()) as $rule) { ?>
            <div>
                <label><?php echo \TS_Functions::__($rule['title']); ?></label>
                <div class="select-wrapper">
                    <select onchange="redirectAfterAction.changeSelectOption(this)" name="<?php echo $redirectAfterActionModel->getActionRuleFormSelectName($action::NAME, $rule['name']); ?>">
                        <option value=""><?php echo \TS_Functions::__('Without redirect'); ?></option>
                        <optgroup label="<?php echo \TS_Functions::__(ProjectSite::TITLE); ?>">
                            <?php

                            /** @var array $currentRedirectAfterAction - Rule combination values */
                            foreach ((new ProjectSite())->getPageLinks($currentRedirectAfterAction[$action::NAME][$rule['name']]['page']) as $pageLink) {
                                ?><option value='<?php echo $pageLink['value']; ?>'<?php echo $redirectAfterActionModel->optionSelected($pageLink); ?>><?php echo $pageLink['title']; ?></option><?php
                            }

                            ?>
                        </optgroup>
                        <optgroup label="<?php echo \TS_Functions::__(ProjectPlatform::TITLE); ?>">
                            <?php

                            foreach ((new ProjectPlatform())->getPageLinks($currentRedirectAfterAction[$action::NAME][$rule['name']]['page']) as $pageLink) {
                                ?><option value='<?php echo $pageLink['value']; ?>'<?php echo $redirectAfterActionModel->optionSelected($pageLink); ?>><?php echo $pageLink['title']; ?></option><?php
                            }

                            ?>
                        </optgroup>
                        <optgroup id="custom_group" label="<?php echo \TS_Functions::__(ProjectCustom::TITLE); ?>">
                            <?php

                            /** @var array $customPageLinks */
                            $customPageLinks = (new ProjectCustom())->getPageLinks($currentRedirectAfterAction[$action::NAME][$rule['name']]['page']);
                            foreach ($customPageLinks as $pageLink) {
                                ?><option custom_element_id="<?php echo $redirectAfterActionModel->getActionRuleFormCustomId($action::NAME, $rule['name']); ?>" value='<?php echo $pageLink['value']; ?>'<?php echo $redirectAfterActionModel->optionSelected($pageLink); ?>><?php echo $pageLink['title']; ?></option><?php
                            }

                            ?>
                        </optgroup>
                    </select>
                </div>
                <span class="custom_elements">
                    <?php foreach ($customPageLinks as $pageLink) { ?>
                        <input id="<?php echo $redirectAfterActionModel->getActionRuleFormCustomId($action::NAME, $rule['name']); ?>" type="text" name="<?php echo $redirectAfterActionModel->getActionRuleFormCustomName($action::NAME, $rule['name']); ?>" value="<?php echo $currentRedirectAfterAction[$action::NAME][$rule['name']]['custom']; ?>" style="<?php echo ($pageLink['active']) ? ' display: inline-block;' : ''; ?>">
                    <?php } ?>
                </span>
            </div>
        <?php } ?>

    </div>
<?php } ?>
<script src="<?php echo \tradersoft\helpers\Assets::findUrl('/js/redirect_after_action.js', 'admin'); ?>?v=1"></script>
<script>

    redirectAfterAction = new RedirectAfterAction();

</script>
