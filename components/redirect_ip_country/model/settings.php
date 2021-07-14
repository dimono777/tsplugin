<?php

namespace tradersoft\components\redirect_ip_country\model;

use tradersoft\helpers\Config;
use tradersoft\helpers\TS_Setting;

/**
 * Class Settings
 *
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class Settings
{
    /** @var string */
    protected static $_ruleSelectName = 'redirect_ip_country';

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return mixed
     */
    public static function getRedirectRuleOptions()
    {
        return Config::get([static::$_ruleSelectName, 'rule', 'options']);
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return string
     */
    public static function getRuleName()
    {
        return static::$_ruleSelectName;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return mixed
     */
    public static function getRuleTitle()
    {
        return Config::get([static::$_ruleSelectName, 'rule', 'title']);
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return mixed
     */
    public static function getBlockTitle()
    {
        return Config::get([static::$_ruleSelectName, 'settings', 'title']);
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return string|null
     */
    public static function isNeedRedirect(){
        return (TS_Setting::get(static::$_ruleSelectName) == RedirectModel::REDIRECT_YES);
    }
    
}