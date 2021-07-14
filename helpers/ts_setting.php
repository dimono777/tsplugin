<?php

namespace tradersoft\helpers;

use tradersoft\cache\TransientCache;
use wpdb;

/**
 * TS_Setting helper for work with table wp_ts_settings.
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class TS_Setting
{
    /** @var string */
    protected static $_cacheKey = 'ts:settings';

    /** @var array */
    protected static $_values = [];

    /**
     * Get value
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public static function get($name, $default = null)
    {
        if (array_key_exists($name, static::$_values)) {
            return self::$_values[$name];
        }

        $value = Arr::get(self::_getValues([$name]), $name, $default);
        self::$_values[$name] = $value;
        return $value;
    }

    /**
     *
     * @return bool
     */
    public static function clearCache()
    {

        TransientCache::clear(self::$_cacheKey);
        return true;
    }

    /**
     * @param string $name
     * @param int|string $value
     *
     * @return bool
     */
    public static function insertValuesIntoDB($name, $value)
    {

        if (empty($name) || !is_string($name)) {
            return false;
        }
        /** @var wpdb $wpdb */
        global $wpdb;

        $result = $wpdb->insert(
            "{$wpdb->prefix}ts_settings",
            [
                'name' => $name,
                'value' => $value,
            ]
        );
        if ($result) {
            self::$_values[$name] = $value;
        }
        return $result;
    }

    /**
     * Get values
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param array $names
     *
     * @return array
     */
    protected static function _getValues(array $names)
    {

        /** @var array $default values */
        $default = array_fill_keys($names, null);
        /** @var array $cache values */
        $cache = self::_getValuesFromCache($names);
        /** @var array $db values */
        $db = self::_getValuesFromDB(
            array_diff_key($names, array_keys($cache)) // names for db query
        );
        // Add new cache
        self::_addCache($db);

        return array_merge($default, $cache, $db);
    }

    /**
     * Get values from cache
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param array $names
     *
     * @return array $result
     */
    protected static function _getValuesFromCache(array $names)
    {

        /** @var array $result */
        $result = [];
        /**
         * Check cache
         */
        $cache = TransientCache::get(self::$_cacheKey);
        if (!$cache) {
            return $result;
        }
        foreach ($names as $name) {
            if (isset($cache[$name])) {
                $result[$name] = $cache[$name];
            }
        }

        return $result;
    }

    /**
     * Get values from DB
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param array $names
     *
     * @return array $result
     */
    protected static function _getValuesFromDB(array $names)
    {
        /** @var wpdb $wpdb */
        global $wpdb;
        if (!$names) {
            return [];
        }
        /** @var array $settings */
        $settings = $wpdb->get_results(
            "SELECT "
                . "`name`, "
                . "`value` "
            . "FROM `{$wpdb->prefix}ts_settings` "
            . "WHERE `name` IN ('"
                . implode("','", $names)
            . "')",
            ARRAY_A
        );

        return array_column($settings, 'value', 'name');
    }

    /**
     * Add new values to cache
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param array $data
     *
     * @return void
     */
    protected static function _addCache(array $data)
    {

        if (empty($data)) {
            return;
        }
        $cacheData = TransientCache::get(self::$_cacheKey);
        if (empty($cacheData)) {
            $cacheData = [];
        }
        TransientCache::set(self::$_cacheKey, array_merge($cacheData, $data));

        return;
    }
}