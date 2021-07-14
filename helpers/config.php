<?php
namespace tradersoft\helpers;

use SplFileInfo;

/**
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Config
{
    private static $_configDir = '/../configs/';
    private static $_cache = [];

    /**
     * For example to use:
     * ```php
     * Config::get('config_file_ame');
     * Config::get('config_file_ame.embedded_data');
     * ```
     *
     * @param string|array $path
     * @param mixed $default
     * @return mixed
     */
    public static function get($path, $default = null)
    {
        if (is_array($path)) {
            $parts = $path;
        } else {
            $parts = explode('.', $path);
        }
        $name = mb_strtolower($parts[0]);

        if (!static::_isConfigInCache($name)) {
            static::_fetchFileConfig($name);
        }

        return Arr::get(self::$_cache, $path, $default);
    }

    /**
     * @param string $name
     * @return bool
     */
    private static function _isConfigInCache($name)
    {
        return isset(self::$_cache[$name]);
    }

    /**
     * @param string $name
     */
    private static function _fetchFileConfig($name)
    {
        $path = dirname(__FILE__)  . self::$_configDir . $name . '.php';

        $file = new SplFileInfo($path);
        if (!$file->isFile()) {
            return;
        }

        $confData = file_get_contents($file->getPathname());
        if (strpos($confData, 'return') === false) {
            return;
        }
        unset($confData);

        $confData = require($file->getPathname());
        if (!is_array($confData)) {
            return;
        }

        self::$_cache[$name] = $confData;
    }

    /**
     * @deprecated
     */
    private static function _scanDir()
    {
        if (!empty(self::$_cache)) {
            return;
        }

        $dir = new \DirectoryIterator(dirname(__FILE__) . self::$_configDir);
        foreach ($dir as $file) {
            if ($file->isFile() && $file->getExtension()=='php') {
                $fileName = $file->getBasename('.php');
                $confData = file_get_contents($file->getPathname());
                if (strpos($confData, 'return') === false) {
                    continue;
                }
                unset($confData);
                $confData = require_once($file->getPathname());
                if (is_array($confData)) {
                    self::$_cache[$fileName] = $confData;
                }
            }
        }
    }
}