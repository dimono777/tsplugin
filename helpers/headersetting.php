<?php
namespace tradersoft\helpers;

class HeaderSetting
{
    const FINANCE_INFO_TYPE_BALANCE = 0;
    const FINANCE_INFO_TYPE_EQUITY = 1;

    /**
     * @return int
     */
    public static function getCurrentFinanceInfoTypeId()
    {
        $type = (int)TS_Setting::get('finance_info_type');
        if (!isset(static::getFinanceInfoTypesList()[$type])) {
            $type = static::FINANCE_INFO_TYPE_BALANCE;
        }

        return $type;
    }

    /**
     * @return string
     */
    public static function getCurrentFinanceInfoTypeLabel()
    {
        $typesList = self::getFinanceInfoTypesList();
        $currentTypeId = self::getCurrentFinanceInfoTypeId();

        return $typesList[$currentTypeId];
    }

    /**
     * @return array
     */
    public static function getFinanceInfoTypesList()
    {
        return [
            static::FINANCE_INFO_TYPE_BALANCE => \TS_Functions::__('Balance'),
            static::FINANCE_INFO_TYPE_EQUITY  => \TS_Functions::__('Equity'),
        ];
    }
}