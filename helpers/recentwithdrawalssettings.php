<?php

namespace tradersoft\helpers;

class RecentWithdrawalsSettings
{
    const SETTING_NAME = 'recent_withdrawals_view_type';
    const AMOUNT_INCLUDING_FEES = 1;
    const AMOUNT_EXCLUDING_FEES = 2;

    /**
     * Get list of amount types
     *
     * @return array
     */
    public static function getAmountTypeList()
    {
        return [
            static::AMOUNT_INCLUDING_FEES => \TS_Functions::__('Amount (including Fees)'),
            static::AMOUNT_EXCLUDING_FEES => \TS_Functions::__('Amount (excluding Fees) + Fees'),
        ];
    }

    /**
     * Get default value
     *
     * @return int
     */
    public static function getDefaultType()
    {
        return self::AMOUNT_INCLUDING_FEES;
    }

    /**
     * Get value from the settings
     *
     * @return int|mixed
     */
    public static function getSettingsValue()
    {
        return TS_Setting::get(RecentWithdrawalsSettings::SETTING_NAME) ?? self::getDefaultType();
    }
}
