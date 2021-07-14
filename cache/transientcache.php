<?php

namespace tradersoft\cache;

/**
 * Class TransientCache
 * @package tradersoft\cache
 * @author Anatolii Lishchynskyi <anatolii.lishchynsky@tstechpro.com>
 */
class TransientCache implements CacheInterface
{

    /**
     * For example to use:
     * ```php
     * TransientCache::get('page_keys_data');
     * ```
     *
     * @param $key string
     * @return mixed
     */
    public static function get($key)
    {
        return get_transient($key);
    }

    /**
     * For example to use:
     * ```php
     * TransientCache::set('page_keys_data', $data);
     * ```
     *
     * @param $key
     * @param $data
     * @return bool False if value was not set and true if value was set.
     */
    public static function set($key, $data)
    {
        return set_transient($key, $data);
    }

    /**
     * For example to use:
     * ```php
     * TransientCache::clear('page_keys_data');
     *
     *
     * @param $key
     * @return bool true if successful, false otherwise
     */
    public static function clear($key)
    {
        return delete_transient($key);
    }

}