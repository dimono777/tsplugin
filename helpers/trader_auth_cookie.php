<?php

namespace tradersoft\helpers;

use TSInit;
use tradersoft\helpers\system\Cookie;

/**
 * Class Trader_Auth_Cookie
 * Work with the trader auth cookie
 * @package tradersoft\helpers
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 */
class Trader_Auth_Cookie
{
    /** Names of platform cookies */
    const HASH_ID_COOKIE_NAME = 'trader-hash-id';
    const HASH_COOKIE_NAME = 'trader-hash';
    const REMEMBER_ME = 'remember-me';
    const POPUPER_SESSION = 'popupersession';

    const DEFAULT_REMEMBER_ME_VALUE = true;

    /** @var array */
    protected static $_cookieNames = [
        self::HASH_ID_COOKIE_NAME,
        self::HASH_COOKIE_NAME,
        self::REMEMBER_ME,
    ];

    /**
     * Set all cookie
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param array $traderInfo
     * @param bool $rememberMe
     */
    public static function set(array $traderInfo, $rememberMe = self::DEFAULT_REMEMBER_ME_VALUE)
    {
        /** @var array */
        $keys = array_combine(
            self::$_cookieNames,
            ['crmHashId', 'traderHash', static::REMEMBER_ME]
        );
        $traderInfo[static::REMEMBER_ME] = (int) $rememberMe;

        /** @var int */
        $expire = ($rememberMe)
            ? static::_getCookieTTL($traderInfo)
            : 0;

        foreach ($keys as $name => $key) {
            Cookie::set(
                $name,
                Arr::get($traderInfo, $key, ''),
                $expire,
                '/',
                TSInit::$app->request->getMainDomain()
            );
        }
    }

    /**
     * Get all cookie
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @return array
     */
    public static function get()
    {
        /** @var array */
        $values = [];

        foreach (self::$_cookieNames as $name) {
            $values[$name] = Cookie::get($name);
        }

        return $values;
    }

    /**
     * Check the data is correct
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @return bool
     */
    public static function isValid()
    {
        foreach (self::get() as $value) {
            if ((!is_string($value) || !trim($value)) && $value !== '0') {
                return false;
            }
        }

        return true;
    }

    /**
     * Checking existence auth cookies
     * @return bool
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public static function checkAuthCookie()
    {
        foreach (self::get() as $value) {
            if ($value === null) {
                return false;
            }
        }

        return true;
    }

    /**
     * Clear cookie
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     */
    public static function clear()
    {

        foreach (self::get() as $name => $value) {
            if ($value !== null) {
                Cookie::delete($name);
            }
        }

        self::_clearPopuperSession();
    }

    /**
     * Getting cookie life time seconds
     * @param $traderInfo
     * @return int
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    protected static function _getCookieTTL($traderInfo)
    {
        return Arr::get($traderInfo, 'cookieTTL', 0);
    }

    /**
     * @author Andrey Fomov
     *
     *
     * @return void
     *
     */
    protected static function _clearPopuperSession()
    {
        Cookie::delete(self::POPUPER_SESSION);
    }

}