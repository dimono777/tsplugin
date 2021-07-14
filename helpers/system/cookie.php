<?php
namespace tradersoft\helpers\system;

use tradersoft\helpers\Request;
use tradersoft\helpers\TS_Setting;

/**
 * Cookie helper
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Cookie
{
    /**
     * @param $name string
     * @param $value mixed
     * @param $expiration integer
     * @param $path string
     * @param $domain string
     * @return bool
     */
    public static function set($name, $value, $expiration = NULL, $path = NULL, $domain = NULL)
    {
        if ($expiration !== 0) {
            // The expiration is expected to be a UNIX timestamp
            $expiration += time();
        }

        // Add the salt to the cookie value
        $value = self::salt($name, $value) . '~' . $value;

        $isSecure = Request::getIsSecureConnection();

        if (PHP_VERSION_ID < 70300) {
            $path .= $isSecure ? '; SameSite=None' : '';

            return setcookie($name, $value, $expiration, $path, $domain, $isSecure, true);
        } else {
            $options = [
                'expires' => $expiration,
                'path' => $path,
                'domain' => $domain,
                'secure' => $isSecure,
                'httponly' => true,
            ];

            if ($isSecure) {
                $options['samesite'] = 'None';
            }

            return setcookie($name, $value, $options);
        }
    }

    /**
     * @param $key string
     * @param $default mixed
     * @return mixed
     */
    public static function get($key, $default = NULL)
    {
        if ( ! isset($_COOKIE[$key])) {
            // The cookie does not exist
            return $default;
        }

        // Get the cookie value
        $cookie = $_COOKIE[$key];

        // Find the position of the split between salt and contents
        $split = strlen(self::salt($key, NULL));

        if (isset($cookie[$split]) AND $cookie[$split] === '~') {
            // Separate the salt and the value
            list ($hash, $value) = explode('~', $cookie, 2);

            if (self::salt($key, $value) === $hash) {
                // Cookie signature is valid
                return $value;
            }

            // The cookie signature is invalid, delete it
            self::delete($key);
        }

        return $default;
    }

    /**
     * @param $name string
     * @return bool
     */
    public static function delete($name)
    {
        // Remove the cookie
        unset($_COOKIE[$name]);

        // Nullify the cookie and make it expire
        return self::set($name, NULL, -1, '/', \TS_Functions::getMainDomain());
    }

    /**
     * @deprecated Not use js cookie!
     * @param $name string
     */
    public static function deleteJS($name)
    {
        if (is_array($name)) {
            foreach ($name as $n) {
                echo "<script language='javascript'>document.cookie = '$n=; expires=Thu, 01 Jan 1970 00:00:01 GMT; domain=.".\TS_Functions::getMainDomain()."; path=/';</script>";
            }
        } else {
            echo "<script language='javascript'>document.cookie = '$name=; expires=Thu, 01 Jan 1970 00:00:01 GMT;domain=.".\TS_Functions::getMainDomain()."; path=/';</script>";
        }
    }

    /**
     * @deprecated Not use js cookie!
     * @param $name string
     * @param $value mixed
     * @param $expiration integer
     * @param $path string
     * @param $domain string
     * @param $withSalt bool
     */
    public static function setJs($name, $value, $expiration = null, $path = null, $domain = null, $withSalt = true)
    {
        if ($expiration !== 0) {
            $expiration += time();
        }
        if (!$path) {
            $path = '/';
        }
        if (!$domain) {
            $domain = \TS_Functions::getMainDomain();
        }

        if ($withSalt) {
            $value = self::salt($name, $value) . '~' . $value;
        }
        echo "<script language='javascript'>document.cookie = '$name="
            . $value . "; expires=" . date('D, d M Y H:i:s e', $expiration) . ";domain=."
            . $domain . "; path=" . $path . "';</script>";
    }

    /**
     * @param $name string
     * @param $value string
     * @return string
     */
    public static function salt($name, $value)
    {
        // Determine the user agent
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : 'unknown';

        return sha1($agent . $name . $value . self::getSalt());
    }

    /**
     * @return string
     */
    public static function getSalt()
    {
        $salt = TS_Setting::get('cookie_salt');
        if (!empty($salt)) {
            return $salt;
        }
        return '';
    }
}