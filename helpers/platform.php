<?php
namespace tradersoft\helpers;

use tradersoft\Application;

// TODO: do not forget to refactor and remove unnecessary methods (according to ticket OPT-19535)
class Platform
{
    /** @deprecated  */
    const PLATFORM_TYPE_NATIVE   = 1;
    /** @deprecated  */
    const PLATFORM_TYPE_EXTENDED = 2;
    /** @deprecated  */
    const PLATFORM_TYPE_NEW = 3;

    const COMMON_SUBDOMAIN_FIELD = 'common_subdomain';
    const ASSETS_SUBDOMAIN_FIELD = 'scripts_subdomain';

    const URL_BASE_ID = 0;
    const URL_BINARY_ID = 1;
    const URL_CFD_ID = 2;
    const URL_TRADE_ID = 3;
    const URL_DEPOSIT_ID = 4;
    const URL_WITHDRAW_ID = 5;
    const URL_MINI_CHART_FRAME = 6;
    const URL_VERIFICATION = 7;
    const URL_CHANGE_PASSWORD = 8;
    const URL_EDIT_DETAILS = 9;
    const URL_SWITCH = 10;
    const URL_SETTING = 11;
    const URL_STAT_CFD = 12;
    const URL_SCRIPT_USERS_INFO = 13;
    const URL_SCRIPT_ASSETS_RSS = 14;
    const URL_SCRIPT_WITHDRAWAL = 15;
    const URL_SCRIPT_CRM_LIB = 16;

    /**
     * @deprecated
     * @return int
     */
    public static function getType()
    {
        $type = (int)TS_Setting::get('platform_type');
        if (!in_array($type, [
            static::PLATFORM_TYPE_NATIVE,
            static::PLATFORM_TYPE_EXTENDED,
            static::PLATFORM_TYPE_NEW])
        ) {
            $type = static::PLATFORM_TYPE_NATIVE;
        }

        return $type;
    }

    public static function getDomain()
    {
        return static::_getDomain(static::COMMON_SUBDOMAIN_FIELD);
    }

    public static function getAssetsDomain()
    {
        return static::_getDomain(static::ASSETS_SUBDOMAIN_FIELD);
    }

    /**
     * Get platform URL by id
     * Base url by default
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param int $linkId
     * @return string
     */
    public static function getURL($linkId = self::URL_BASE_ID)
    {
        $config = static::getPlatformURLsConfig();
        $isAsset = Arr::get($config, "{$linkId}.isAsset", false);
        $domain = $isAsset ? static::getAssetsDomain() : static::getDomain();
        $urlPath = static::_getUrlPathById($linkId);

        return rtrim($domain, '/') . '/' . ltrim($urlPath, '/');
    }

    /**
     * @deprecated
     * Get platform links
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return array $result
     */
    public static function getLinks()
    {
        $result = [];

        /** @var array $platformConfig */
        $platformConfig = self::_getPlatformConfig();

        /** @var string $platformDomain */
        $platformDomain = self::getDomain();

        foreach ($platformConfig['urls'] as $groupName => $linkGroup) {
            foreach ($linkGroup as $linkId => $link) {
                $link['uri'] = "{$platformDomain}{$link['uri']}";

                $result[$linkId] = $link;
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public static function getPlatformURLsConfig()
    {
        static $cache = [];
        if (empty($cache)) {
            $cache = Config::get('platform_urls');
        }
        return $cache;
    }

    /**
     * Get platform domain
     * @return string
     */
    private static function _getDomain($subdomenType)
    {
        Platform::_initDBConfigFromFileConfig(); // #TODO: do not forget to remove this line (according to ticket OPT-19535)

        $subDomain = TS_Setting::get($subdomenType);

        if ($subDomain) {
            $subDomain = "{$subDomain}.";
        }

        return '//' . $subDomain . Application::getInstance()->request->getMainDomain();
    }

    /**
     * @deprecated TODO: do not forget to remove (according to ticket OPT-19535)
     * @return array
     */
    private static function _getPlatformConfig()
    {
        if (self::getType() == static::PLATFORM_TYPE_EXTENDED) {
            return Config::get('platform_extended');
        } elseif (self::getType() == static::PLATFORM_TYPE_NEW) {
            return Config::get('platform_new');
        } else {
            return Config::get('platform');
        }
    }

    /**
     * @param integer $urlId
     */
    private static function _getUrlAliasById($urlId)
    {
        $config = static::getPlatformURLsConfig();

        return isset($config[$urlId]['ts_setting_name'])
            ? $config[$urlId]['ts_setting_name']
            : 'undefined';
    }

    /**
     * @param $linkId
     * @return null|string
     */
    private static function _getUrlPathById($linkId)
    {
        return TS_Setting::get(static::_getUrlAliasById($linkId));
    }

    /**
     * @deprecated
     * Copy config domains settings to db depending current platform type
     * TODO: do not forget to remove (according to ticket OPT-19535)
     *
     * @param null $subdomainAlias
     * @return bool|null|string
     */
    private static function _initDBConfigFromFileConfig()
    {
        // TS_Setting::clearCache();

        if (TS_Setting::get(self::COMMON_SUBDOMAIN_FIELD)) {
            return;
        }

        $platformConfig = self::_getPlatformConfig();

        // 1. Init subdomains
        TS_Setting::insertValuesIntoDB(static::COMMON_SUBDOMAIN_FIELD, $platformConfig['subDomain']);
        TS_Setting::insertValuesIntoDB(static::ASSETS_SUBDOMAIN_FIELD, $platformConfig['scriptSubDomain']);

        // 2. Init urls paths
        // 2.1. collect params array from config file
        foreach ($platformConfig['urls'] as $groupName => $linkGroup) {
            foreach ($linkGroup as $linkId => $link) {
                $fileConfigData[$linkId] = $link['uri'];
            }
        }

        // 2.2. check url params which loaded from DB
        foreach (static::getPlatformURLsConfig() as $urlId => $paramData) {
            TS_Setting::insertValuesIntoDB(
                $paramData['ts_setting_name'],
                $fileConfigData[$urlId]
            );
        }
        
        TS_Setting::clearCache();
    }
}