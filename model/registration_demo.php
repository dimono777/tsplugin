<?php
namespace tradersoft\model;

use tradersoft\helpers\Platform;

/**
 * Demo registration model
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Registration_Demo extends Registration
{
    public function getRedirectUrlAfter()
    {
        return Platform::getURL(Platform::URL_SWITCH);
    }
}