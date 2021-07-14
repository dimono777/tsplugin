<?php

namespace tradersoft\helpers;


use CURLFile;
use InvalidArgumentException;

/**
 * Interlayer helper.
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Interlayer
{
    private static $_curl;

    /**
     * @param string $url
     * @param array  $params
     * @param array  $files
     *
     * @return mixed
     */
    public static function sendRequest($url, array $params = [], array $files = []) {
        if (!$url) {
            return false;
        }
        $params['secretKey'] = self::_getSecretKey();
        $params['requesterIp'] = Ip::getCurrent();
        $params = self::_prepareRequestParameters($params);
        foreach ($files as $key => $filename) {
            if (!file_exists($filename) || !is_readable($filename)) {
                throw new InvalidArgumentException(
                    "Failed to read file with name {$filename} during request sending."
                );
            }
            $params[$key] = new CURLFile($filename);
        }
        $api_domain = self::_getInterlayerDomain();

        $ch = self::_retrieveCurlResource();
        $curlOptions = [
            CURLOPT_URL        => rtrim($api_domain, '/') . '/' . ltrim($url, '/'),
            CURLOPT_POSTFIELDS => $params
        ];
        curl_setopt_array(
            $ch,
            $curlOptions
        );

        $data = curl_exec($ch);

        return $data;
    }

    /**
     * Get interlayer domain from ts_settings
     *
     * @return string
     */
    protected static function _getInterlayerDomain()
    {
        return TS_Setting::get('interlayer_domain');
    }

    /**
     * Get interlayer secret key from ts_settings
     *
     * @return string
     */
    protected static function _getSecretKey()
    {
        return TS_Setting::get('interlayer_secret_key');
    }

    /**
     * @param string $json
     * @param bool   $assoc
     *
     * @return mixed
     */
    protected static function jsonDecode($json, $assoc = true)
    {
        return json_decode($json, $assoc);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    private static function _prepareRequestParameters(array $params)
    {
        $preparedParams = [];
        $queue = self::_buildQueue($params);
        while ($queue) {
            list($key, $val) = array_shift($queue);
            if (is_array($val)) {
                if ($val) {
                    $queue = array_merge($queue, self::_buildQueue($val, $key));
                }
            } else {
                $preparedParams[$key] = $val;
            }
        }
        return $preparedParams;
    }

    /**
     * @param array $params
     * @param int|string|null $parentKey
     *
     * @return array
     */
    private static function _buildQueue(array $params, $parentKey = null)
    {
        return array_map(
            function ($paramKey, $paramValue) use ($parentKey) {
                if ($parentKey) {
                    $paramKey = "{$parentKey}[{$paramKey}]";
                }
                return [$paramKey, $paramValue];
            },
            array_keys($params),
            $params
        );
    }

    /**
     * @return resource
     */
    private static function _retrieveCurlResource()
    {
        if (!self::$_curl) {
            self::$_curl = curl_init();
            curl_setopt_array(
                self::$_curl,
                [
                    CURLOPT_POST           => true,
                    CURLOPT_RETURNTRANSFER => true
                ]
            );
        }
        return self::$_curl;
    }
}