<?php
namespace tradersoft\helpers\system;

use tradersoft\helpers\Config;
use tradersoft\helpers\Arr;
use Exception;

class PageKey
{
    const KEY_AML_VERIFICATION_FORM = 'TS-AML-VERIFICATION-FORM';
    const KEY_CONTACT_US = 'TS-CONTACT-US';
    const KEY_FORMS = 'TS-FORMS';

    /**
     * Get pages action
     */
    public static function getPagesActions()
    {
        $keys = [];
        foreach (self::getPagesKey() as $key => $action) {
            $keys[$key] = Arr::get($action, 'action', '');
        }
        return $keys;
    }

    /**
     * Get pages description
     */
    public static function getPagesDescription()
    {
        $descriptions = [];
        foreach (self::getPagesKey() as $key => $action) {
            $descriptions[$key] = Arr::get($action, 'description');
        }
        return $descriptions;
    }

    public static function getPagesKey()
    {
        return Config::get('page_keys', []);
    }

    /**
     * @param       $key
     * @param array $params
     *
     * @return string
     * @throws Exception
     */
    public static function getPageShortCode($key, array $params = [])
    {
        $keys = static::getPagesKey();
        $pageKey = '[' . $key . ']';

        if (!array_key_exists($pageKey, $keys)) {
            throw new Exception("Unknown page key. key=$key");
        }

        $strParams = '';
        foreach ($params as $paramKey => $paramValue) {
            $strParams .= " $paramKey=`$paramValue`";
        }

        return '[' . $key . $strParams . ']';
    }
}