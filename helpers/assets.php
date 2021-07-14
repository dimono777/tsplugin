<?php

namespace tradersoft\helpers;

use TS_Functions;

/**
 * Class Assets
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package tradersoft\helpers
 */
class Assets
{
    /**
     * Function getActualContainer
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param null $folder
     *
     * @return string
     */
    public static function getActualContainer($folder = null)
    {

        $address = '';
        foreach (static::getContainersList($folder) as $path => $address) {
            if (is_dir($path)) {
                return $address;
            }
        };

        return $address;
    }
    /**
     * Function findUrl
     * Try to get URL of media file from different places(them, default contriners in plugin, etc)
     * and return if file exists with founded address
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $file
     * @param string|null $additionalFolder
     * @param array $getParams
     *
     * @return string
     */
    public static function findUrl($file, $additionalFolder = null, array $getParams = [])
    {
        $file = trim($file, '/');
        if ($additionalFolder) {
            $additionalFolder = trim($additionalFolder, '/');
        }

        foreach (static::getContainersList($additionalFolder) as $path => $address) {
            if (is_dir($path) && file_exists($path . ltrim($file, '/'))) {

                return Link::addParams(
                    $address . ltrim($file, '/'),
                    $getParams
                );
            }
        };
        return Link::addParams(
            (($additionalFolder) ? "/$additionalFolder" : "") . "/$file",
            $getParams
        );
    }

    /**
     * Function clarifyAddress
     *  Modify address if it doesn't contain a host. try to use correct theme folder, etc
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param string $source
     * @param string|null $themeContainer
     *
     * @return string
     */
    public static function clarifyAddress($source, $themeContainer = null)
    {

        $source = trim($source);
        if (!$source) {
            return $source;
        }
        $sourceParts = wp_parse_url($source);
        if (!isset($sourceParts['path'])) {
            $source = '';
        } elseif (
            !isset($sourceParts['host'])
            && strpos(ltrim($sourceParts['path'], '/'), 'wp-content/') === 0
        ) {
            $source = '//' . \TS_Functions::getHostName() . '/' . ltrim($source, '/');
        } elseif (
            !isset($sourceParts['host'])
            && strpos($sourceParts['path'], 'wp-content/') === false
        ) {
            $source = self::findUrl($source, $themeContainer);
        }

        return $source;
    }

    /**
     * Function getPossibleContainers
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param string $internalPath
     *
     * @return array
     */
    public static function getContainersList($internalPath = 'default')
    {
        if (null === $internalPath) {
            $internalPath = \TS_Functions::isThemeExist() ? \TS_Functions::getCurrentTheme() : 'default';
        }
        $internalPath = trim($internalPath, '/');

        $paths = [];


        if ($internalPath) {
            $path = rtrim(get_template_directory(), '/') . "/plugins/tradersoft/{$internalPath}/";
            $address = rtrim(get_template_directory_uri(), '/') . "/plugins/tradersoft/{$internalPath}/";
            $paths[$path] = $address;
        }

        if (!$internalPath || $internalPath == 'default') {
            $path = rtrim(get_template_directory(), '/') . "/plugins/tradersoft/";
            $address = rtrim(get_template_directory_uri(), '/') . "/plugins/tradersoft/";
            $paths[$path] = $address;
        }

        if ($internalPath) {
            $path = rtrim(get_template_directory(), '/') . "/plugin_templates/{$internalPath}/";
            $address = rtrim(get_template_directory_uri(), '/') . "/plugin_templates/{$internalPath}/";
            $paths[$path] = $address;
        }

        if (!$internalPath || $internalPath == 'default') {
            $path = rtrim(get_template_directory(), '/') . "/plugin_templates/";
            $address = rtrim(get_template_directory_uri(), '/') . "/plugin_templates/";
            $paths[$path] = $address;
        }

        if ($internalPath) {
            $path = rtrim(TS_DOCROOT, '/') . "/templates/{$internalPath}/";
            $address = rtrim(plugins_url("templates/{$internalPath}/", TS_PLUGIN_BASENAME), '/') . "/";
            $paths[$path] = $address;
        }

        if ($internalPath !== 'default') {
            $path = rtrim(TS_DOCROOT, '/') . "/templates/default/";
            $address = rtrim(plugins_url("templates/default/", TS_PLUGIN_BASENAME), '/') . "/";
            $paths[$path] = $address;
        }

        if (!$internalPath || $internalPath == 'default') {
            $path = rtrim(TS_DOCROOT, '/') . "/templates/";
            $address = rtrim(plugins_url('templates', TS_PLUGIN_BASENAME), '/') . "/";
            $paths[$path] = $address;
        }

        return $paths;

    }
}