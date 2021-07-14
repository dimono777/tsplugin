<?php
namespace tradersoft\helpers;

class RegulationSetting
{
    const EDIT_PROFILE_TYPE_DEFAULT = 0;
    const EDIT_PROFILE_TYPE_ASIC = 1;
    const EDIT_PROFILE_TYPE_SAASIC = 2;

    const EDIT_PROFILE_TRIGGER_BEFORE_FTD = 1;
    const EDIT_PROFILE_TRIGGER_AFTER_FTD = 2;

    /**
     * @return int
     */
    public static function getType()
    {
        $type = (int)TS_Setting::get('regulation_profile_type');
        if (!isset(static::getTypes()[$type])) {
            $type = static::EDIT_PROFILE_TYPE_DEFAULT;
        }

        return $type;
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            static::EDIT_PROFILE_TYPE_DEFAULT => \TS_Functions::__('Default'),
            static::EDIT_PROFILE_TYPE_ASIC    => \TS_Functions::__('ASIC'),
            static::EDIT_PROFILE_TYPE_SAASIC  => \TS_Functions::__('SAASIC'),
        ];
    }

    /**
     * @return int
     */
    public static function getTriggerFTD()
    {
        $type = (int)TS_Setting::get('regulation_profile_trigger');
        if (!isset(static::getTypes()[$type])) {
            $type = static::EDIT_PROFILE_TRIGGER_BEFORE_FTD;
        }

        return $type;
    }

    /**
     * @return array
     */
    public static function getTriggersFTD()
    {
        return [
            static::EDIT_PROFILE_TRIGGER_BEFORE_FTD => \TS_Functions::__('BeforeFTD'),
            static::EDIT_PROFILE_TRIGGER_AFTER_FTD  => \TS_Functions::__('AfterFTD'),
        ];
    }

}