<?php

namespace tradersoft\model\redirect_after_action\abstracts;

use tradersoft\helpers\Config;
use tradersoft\helpers\TS_Setting;

/**
 * Class Actions
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 */
abstract class Actions
{
    const NAME = '';

    /**
     * Get action rules from config
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return array
     */
    public function getRules()
    {
        return Config::get('redirect_after_action.rules.actions.' . static::NAME);
    }

    /**
     * Get action combinations of rules from DB
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return array
     */
    public function getRuleCombinations()
    {
        $result = [];

        // Trying to get a value from the DB
        if (!($setting = TS_Setting::get($this->getSettingName()))) {
            return $result;
        }

        // Trying to decode the setting value
        if (!($setting = json_decode($setting, true))) {
            return $result;
        }

        return $setting;
    }

    /**
     * Get action setting name
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return string
     */
    public function getSettingName()
    {
        /** @var string $prefix - Prefix for setting name */
        $prefix = 'redirect_after_';

        return $prefix . static::NAME;
    }
}