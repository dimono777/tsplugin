<?php

namespace tradersoft\helpers\system;

use tradersoft\helpers\Config;

/**
 * Class PlatformTypeSettings
 * Platform type settings for TraderSoft -> Platform Settings
 *
 * @package tradersoft\helpers\system
 */
class PlatformTypeSettings
{
    const CLASSIC = 1;
    const MODERN = 2;

    /**
     * Get types list
     * [id => name]
     *
     * @return array
     */
    public static function getTypesList()
    {
        return [
            static::CLASSIC => \TS_Functions::__('Classic'),
            static::MODERN => \TS_Functions::__('Modern'),
        ];
    }

    /**
     * Get default type
     *
     * @return int
     */
    public static function getDefaultType()
    {
        return self::CLASSIC;
    }

    /**
     * Get default types settings for preload
     *
     * @return array
     */
    public static function getPreloadedSettings()
    {
        return Config::get('platform_type_settings', []);
    }
}