<?php

namespace tradersoft\components;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Link;
use TSInit;

/**
 * Class GoogleAnalytics
 * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
 */
class GoogleAnalytics
{
    /**
     * Google Analytics client ID parameter name
     */
    const CLIENT_ID_FIELD = '_ga';
    const CLIENT_ID_COUNTER = '_gaCounter';
    const CLIENT_ID_CUSTOM_FIELD = '_custom_ga';
    
    protected static $_clientId;

    /**
     * Adding _ga cookie to url
     *
     * @param string $url
     *
     * @return string
     *
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public static function addClientIdToUrl($url)
    {
        $clientId = Arr::get($_COOKIE, static::CLIENT_ID_FIELD);
        if (!$clientId) {
            return $url;
        }

        parse_str(parse_url($url, PHP_URL_QUERY), $query);
        if (!empty($query[static::CLIENT_ID_FIELD])) {
            if ($query[static::CLIENT_ID_FIELD] == $clientId) {
                return $url;
            }
            $url = Link::removeGetParamFromUrl(static::CLIENT_ID_FIELD, $url);
        }

        return Link::addParams($url, [static::CLIENT_ID_FIELD => $clientId]);
    }

    /**
     * Setting client ID t osession
     *
     * @return bool
     *
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public static function setClientId()
    {
        static $clientId;
        if (!is_null($clientId)) {
            return (bool) $clientId;
        }
        $clientId = Arr::get($_GET, static::CLIENT_ID_FIELD, false);
        if (!$clientId) {
            return false;
        }

        $_SESSION[static::CLIENT_ID_FIELD] = $clientId;
        $_SESSION[static::CLIENT_ID_COUNTER] = 2;

        return true;
    }

    /**
     * Getting client identifier from session
     *
     * @return bool|mixed|null
     *
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public static function getClientId()
    {
        static::setClientId();
        
        $clientId = Arr::get($_SESSION, static::CLIENT_ID_FIELD);
        $counter =  Arr::get($_SESSION, static::CLIENT_ID_COUNTER, 1);
        if (!$clientId) {
            return null;
        }
    
         $counter--;
        
        if ($counter <= 0) {
            unset(
                $_SESSION[static::CLIENT_ID_FIELD],
                $_SESSION[static::CLIENT_ID_COUNTER]
            );
        } else {
            $_SESSION[static::CLIENT_ID_COUNTER] = $counter;
        }
    
        return static::_parseClientId($clientId);
    }

    /**
     * Parsing client ID from `_ga` identifier
     *
     * @param string $gaId
     *
     * @return string|null
     *
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public static function _parseClientId($gaId)
    {
        if (!$gaId) {
            return null;
        }

        $parts = explode('.', $gaId);
        if (count($parts) != 4) {
            return null;
        }

        return implode('.', array_slice($parts, 2, 2));
    }
}