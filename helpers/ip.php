<?php

namespace tradersoft\helpers;

class Ip
{
    /**
     * @var string|null
     */
    protected static $clientIP;

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return null|string
     */
    public static function getCurrent()
    {

        if (static::$clientIP === null) {
            $trustedIpHeaders = Arr::get(Config::get('request_params'), 'trustedIpHeaders');
            foreach ($trustedIpHeaders as $header) {
                $_ips = explode(",", Arr::get($_SERVER, $header));
                foreach ($_ips as $ip) {
                    $ip = trim($ip);
                    if ($ip && static::validateIp($ip)) {
                        static::$clientIP = addslashes($ip);

                        return static::$clientIP;
                    }
                }
            }
            static::$clientIP = '';
        }

        return static::$clientIP;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $ip
     *
     * @return bool
     */
    public static function validateIp($ip)
    {

        return (bool)filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_NO_PRIV_RANGE
        );
    }
}
