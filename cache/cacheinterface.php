<?php

namespace tradersoft\cache;

/**
 * Interface CacheInterface
 * @package tradersoft\cache
 */
interface CacheInterface
{

    public static function get($key);

    public static function set($key, $data);

    public static function clear($key);

}