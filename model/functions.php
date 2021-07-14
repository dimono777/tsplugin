<?php

use tradersoft\helpers\Interlayer_Crm;
use tradersoft\helpers\Arr;
use tradersoft\helpers\Ip;
use tradersoft\helpers\Link;
use tradersoft\helpers\Page;
use tradersoft\helpers\system\Cookie;
use tradersoft\helpers\Platform;
use tradersoft\helpers\multi_language\Multi_Language;

class TS_Functions {

    public static $cache;

    private static $_templatePath = '';

    /**
     * @deprecated Use TSInit::$app->request->userIP
     */
    public static function getRealIpAddr() {
        return Ip::getCurrent() ?: '';
    }

    /**
     * @deprecated Use TSInit::$app->request->isLocal
     */
    public static function isLocal() 
    {
        return strpos($_SERVER['HTTP_HOST'], '.local') > 0;
    }

    public static function getHostName() 
    {
        return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : false;
    }

    /**
     * @return mixed
     * @deprecated
     */
    public static function getSecretKey() {
        global $wpdb;
        if (isset(self::$cache['secretKey'])) {
            return self::$cache['secretKey'];
        } else {
            self::$cache['secretKey'] = $wpdb->get_var("SELECT `value` FROM `$wpdb->prefix" . "ts_settings" . "` WHERE `name` = 'secret_key' LIMIT 1");
            return self::$cache['secretKey'];
        }
    }

    /**
     * @return mixed
     * @deprecated
     */
    public static function getOperatorName() {
        global $wpdb;
        if (isset(self::$cache['operatorName'])) {
            return self::$cache['operatorName'];
        } else {
            self::$cache['operatorName'] = $wpdb->get_var("SELECT `value` FROM `$wpdb->prefix" . "ts_settings" . "` WHERE `name` = 'operator_name' LIMIT 1");
            return self::$cache['operatorName'];
        }
    }

    /**
     * @return mixed
     * @deprecated
     */
    public static function getAPIDomain() {
        global $wpdb;
        if (isset(self::$cache['apiDomain'])) {
            return self::$cache['apiDomain'];
        } else {
            self::$cache['apiDomain'] = $wpdb->get_var("SELECT `value` FROM `$wpdb->prefix" . "ts_settings" . "` WHERE `name` = 'api_domain' LIMIT 1");
            return self::$cache['apiDomain'];
        }
    }

    /**
     * Function getCookiesDomainLevel
     *  Try to get int value of coocikes domain level. 1st - get from cache, or from DB, or return default value (int) 2
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return int
     */
    public static function getCookiesDomainLevel() {
        global $wpdb;
        if (!isset(self::$cache['cookieDomainLvl'])) {
            self::$cache['cookieDomainLvl'] = (
                    $wpdb->get_var(
                        "SELECT `value` FROM `$wpdb->prefix" . "ts_settings" . "` WHERE `name` = 'cookies_domain_level' LIMIT 1"
                    )
                )
                ?
                : 2;
        }
        return self::$cache['cookieDomainLvl'];
    }

    /**
     * @deprecated use tradersoft\helpers\system\Cookie::getSalt()
     */
    public static function getSalt() {
        global $wpdb;
        if (isset(self::$cache['cookie_salt'])) {
            return self::$cache['cookie_salt'];
        } else {
            self::$cache['cookie_salt'] = $wpdb->get_var("SELECT `value` FROM `$wpdb->prefix" . "ts_settings" . "` WHERE `name` = 'cookie_salt' LIMIT 1");
            return self::$cache['cookie_salt'];
        }
    }

    /**
     * @param string $url
     * @param array  $params
     * @param string $file
     *
     * @return bool|mixed
     * @deprecated use {@code Interlayer_crm} instead
     */
    public static function sendRequest($url, $params, $file = '') {

        if ( ! $url) {
            return false;
        }

        $params['operatorName'] = self::getOperatorName();
        $crc = strtoupper(md5(self::getSecretKey() . urldecode(http_build_query($params))));
        $params['CRC'] = $crc;

        $requestData = [
            'data' => json_encode($params)
        ];

        $api_domain = self::getAPIDomain();
        if (substr($api_domain, -1) != '/' AND substr($url, 0, 1) != '/')
        {
            $api_domain .= '/';
        }
        if (substr($api_domain, -1) == '/' AND substr($url, 0, 1) == '/')
        {
            $api_domain .= substr($api_domain, 0, strlen($api_domain) - 2);
        }

        $ch = curl_init($api_domain . $url);

        if ($file) {
            $requestData['file'] = new CURLFile($file);
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
        }

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $data = curl_exec($ch);

        curl_close($ch);
        
        return $data;
    }

    /**
     * @deprecated Use \TSInit::$app->request->redirect()
     */
    public static function redirect($url) {
        do_action('tradersoft/before_redirect');
        Header("Location: " . $url);
        exit;
    }

    public static function redirectJS($url) {
        do_action('tradersoft/before_redirect');
        echo '<script>window.location = "' . $url . '"</script>';
    }

    /**
     * @deprecated Use \TSInit::$app->request->getMainDomain()
     */
    public static function getMainDomain($step = null) {
        $parts = explode('.', self::getHostName());
        $count = sizeof($parts);
        if ($step === null) {
            $step = self::getCookiesDomainLevel();
        }
        if ($count >= $step) {
            $return = array();
            while ($step > 0) {
                $return[] = $parts[$count - $step];
                $step--;
            }

            return implode('.', $return);
        }
        return false;
    }

    public static function redirectToPlatform() {
        self::redirect(Platform::getDomain());
        return true;
    }

    public static function redirectToPlatformJS() {
        self::redirectJS(Platform::getDomain());
        return true;
    }

    /**
     * @deprecated Use TSInit::$app->trader->isGuest
     */
    public static function isLogged() {
        return !TSInit::$app->trader->isGuest;
    }

    /**
     * @deprecated use tradersoft\helpers\system\Cookie::salt()
     */
    public static function salt($name, $value) 
    {
        // Determine the user agent
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : 'unknown';

        return sha1($agent.$name.$value.self::getSalt());
    }

    /**
     * @deprecated use tradersoft\helpers\system\Cookie::set()
     */
    public static function setCookie($name, $value, $expiration = NULL, $path = NULL, $domain = NULL) 
    {
        if ($expiration !== 0)
        {
            // The expiration is expected to be a UNIX timestamp
            $expiration += time();
        }

        // Add the salt to the cookie value
        $value = self::salt($name, $value).'~'.$value;

        return setcookie($name, $value, $expiration, $path, $domain, true, true);
    }

    /**
     * @deprecated use tradersoft\helpers\system\Cookie::get()
     */
    public static function getCookie($key, $default = NULL) 
    {
        if ( ! isset($_COOKIE[$key]))
        {
            // The cookie does not exist
            return $default;
        }

        // Get the cookie value
        $cookie = $_COOKIE[$key];

        // Find the position of the split between salt and contents
        $split = strlen(self::salt($key, NULL));

        if (isset($cookie[$split]) AND $cookie[$split] === '~')
        {
                // Separate the salt and the value
                list ($hash, $value) = explode('~', $cookie, 2);

                if (self::salt($key, $value) === $hash)
                {
                        // Cookie signature is valid
                        return $value;
                }

                // The cookie signature is invalid, delete it
                self::deleteCookie($key);
        }

        return $default;
    }

    /**
     * @deprecated use tradersoft\helpers\system\Cookie::delete()
     */
    public static function deleteCookie($name)
    {
        // Remove the cookie
        unset($_COOKIE[$name]);

        // Nullify the cookie and make it expire
        return self::setCookie($name, NULL, -1, '/', static::getMainDomain());
    }

    /**
     * @deprecated use tradersoft\helpers\system\Cookie:: deleteJS()
     */
    public static function deleteCookieJS($name)
    {
        if (is_array($name))
        {
            foreach ($name as $n)
            {
                echo "<script language='javascript'>document.cookie = '$n=; expires=Thu, 01 Jan 1970 00:00:01 GMT; domain=.".static::getMainDomain()."; path=/';</script>";
            }
        }
        else 
        {
            echo "<script language='javascript'>document.cookie = '$name=; expires=Thu, 01 Jan 1970 00:00:01 GMT;domain=.".static::getMainDomain()."; path=/';</script>";
        }
    }

    /**
     * @deprecated Not use js cookie!
     */
    public static function setCookieJs($name, $value, $expiration = null, $path = null, $domain = null, $withSalt = true)
    {
        if ($expiration !== 0) {
            $expiration += time();
        }
        if (!$path) {
            $path = '/';
        }
        if (!$domain) {
            $domain = static::getMainDomain();
        }

        if ($withSalt) {
            $value = self::salt($name, $value) . '~' . $value;
        }
        echo "<script language='javascript'>document.cookie = '$name="
            . $value . "; expires=" . date('D, d M Y H:i:s e', $expiration) . ";domain=."
            . $domain . "; path=" . $path . "';</script>";
    }

    public static function getBrowserLang()
    {
        $accept_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
        return strtolower(substr($accept_language, 0, 2));
    }
    
    public static function isThemeExist()
    {
        return is_dir(dirname(__FILE__) . '/../templates/' . self::getCurrentTheme());
    }

    /**
     * Function getThemePath
     * 
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param string|null $folder
     *
     * @return string
     */
    public static function getThemePath($folder = null)
    {
        return \tradersoft\helpers\Assets::getActualContainer($folder);
    }

    public static function getCurrentTheme()
    {
        $theme = wp_get_theme();
        if (!empty($theme)) {
            $theme = str_replace(" ", "_", $theme);
        }
        return $theme;
    }

    public static function issetLink($key, array $params = [])
    {
        return !empty(Page::getIdsByKey($key, $params));
    }

    /**
     * @deprecated use \tradersoft\helpers\Link::getForPageWithKey()
     * @param string $key
     * @return mixed|string
     */
    public static function getLink($key)
    {
        return Link::getForPageWithKey($key);
    }

    public static function getTermsLink()
    {
        return Link::getForPageWithKey('[TS-TERMS-AND-CONDITIONS]');
    }

    public static function getAuthorisationLinkHtml($authLinkText = 'Log In')
    {
        if (self::issetLink('[TS-AUTHORIZATION]')) {
            return '<a href="' . Link::getForPageWithKey("[TS-AUTHORIZATION]") . '" class="oa ts-btn ts-btn-auth">' . \TS_Functions::__($authLinkText) . '</a>';
        } else {
            return '<a href="#" data-toggle="modal" class="oa ts-btn ts-btn-auth" data-target="#log-in">' . \TS_Functions::__($authLinkText) . '</a>';
        }
    }

    public static function getPost($key, $default = NULL)
    {
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    
    public static function formWasSubmit() 
    {
        return isset($_POST['tradersoft_submit']);
    }

    public static function isFormSubmit($form)
    {
        return isset($_POST['tradersoft_submit']) && $_POST['tradersoft_submit'] == $form;
    }
    
    public static function andCondition($data = array()) 
    {
        foreach ($data as $v)
        {
            if ( ! $v) return false;
        }
        return true;
    }
    
    public static function isLogoutPage() 
    {
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        return strpos($uri, '/logout') === 0;
    }

    /**
     * @deprecated  use \tradersoft\helpers\Platform::getDomain()
     * Get platform domain
     * @return string
     */
    public static function getPlatformDomain()
    {
        return Platform::getDomain();
    }

    /**
     * @deprecated  use \tradersoft\helpers\Platform::getURL()
     * Get platform URL by id
     * Base url by default
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param int $linkId
     * @return string
     */
    public static function getPlatformURL($linkId = Platform::URL_BASE_ID)
    {
        return Platform::getURL($linkId);
    }
    
    /*
     * @deprecated Use Interlayer_Crm::getPhoneCodeByIP()
     */
    public static function getPhoneCodeByIP()
    {
        return Interlayer_Crm::getPhoneCodeByIP() ?: null;
    }

    /**
     * @deprecated Use Interlayer_Crm::getCountryByIP(TSInit::$app->request->userIP)
     */
    public static function getCountryByIP()
    {
        return Interlayer_Crm::getCountryByIP(TSInit::$app->request->userIP) ?: null;
    }

    /**
     * @deprecated Use Interlayer_Crm::getCountriesAll()
     */
    public static function getCountryList()
    {
        return Interlayer_Crm::getCountriesAll() ?: null;
    }

    /**
     * @deprecated use \TSInit::$app->request->getLink()
     */
    public static function link($url) 
    {
        if (substr($url, 0, 1) == '/')
        {
            $url = substr($url, 1);
        }
        return Multi_Language::getInstance()->getHomeUrl() . $url;
    }
    
    /**
     * Get browser iso-2 language
     * @return string
     */
    public static function getBrowserISO()
    {
        $accept_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
        $lang = substr($accept_language, 0, 2);
        return $lang;
    }
    
    public static function generatePassword($length = 8) 
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) 
        {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    public static function switchLanguage() 
    {
        $current_language = Multi_Language::getInstance()->getCurrentLanguage();
        if ($current_language == 'en') {
            $lastLanguage = self::getCookie('lastLanguage');
            if ( ! $lastLanguage) {
                Multi_Language::getInstance()->switchLang(self::getBrowserISO(), true);
            } else {
                $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : false;
                if ($referer && preg_match('/^https?:\/\/trade\.' . $_SERVER['HTTP_HOST'] . '/', $referer)) {
                    Multi_Language::getInstance()->switchLang($lastLanguage, true);
                }
            }
        }
    }
    
    public static function saveLanguage() 
    {
        $lastLanguage = Cookie::get('lastLanguage');
        $currentLanguage = Multi_Language::getInstance()->getCurrentLanguage();
        if ($lastLanguage != $currentLanguage) {
            Cookie::set(
                'lastLanguage',
                $currentLanguage,
                60*60*24*30,
                '/',
                \TSInit::$app->request->getMainDomain()
            );
        }
    }

    /**
     * Return the current language of the site
     * @deprecated Use \tradersoft\helpers\multi_language\Multi_Language::getInstance()->getCurrentLanguage();
     * @return string;
     */
    public static function getCurrentLanguage()
    {
        return Multi_Language::getInstance()->getCurrentLanguage();
    }

    /**
     * Get active languages
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @deprecated Use \tradersoft\helpers\multi_language\Multi_Language::getInstance()->getActiveLanguages();
     * @return array
     */
    public static function getActiveLanguages()
    {
        return Multi_Language::getInstance()->getActiveLanguages();
    }

    public static function camel2words($name, $ucwords = true)
    {
        $label = trim(strtolower(str_replace([
            '-',
            '_',
            '.',
        ], ' ', preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $name))));

        return $ucwords ? ucwords($label) : $label;
    }

    /**
     * Retrieve a single key from an array. If the key does not exist in the
     * array, the default value will be returned instead.
     *
     *     // Get the value "username" from $_POST, if it exists
     *     $username = Arr::get($_POST, 'username');
     *
     *     // Get the value "sorting" from $_GET, if it exists
     *     $sorting = Arr::get($_GET, 'sorting');
     *
     * @param   array   $array to extract from
     * @param   string  $key name
     * @param   mixed   $default value
     * @return  mixed
     */
    public static function arrGet($array, $key, $default = NULL)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    /**
     * get current url protocol
     *
     * @author Roman Lazarskiy <roman.lazarskiy@tstechpro.com>
     *
     * @return string
     */
    public static function getProtocol()
    {
        $https = tradersoft\helpers\Arr::get($_SERVER, 'HTTPS');

        return ($https && $https !== 'off')
            ? 'https'
            : 'http';
    }

    /**
     * Function initNotAuthedUID
     * Try to get notAuthedUID and set it to cookies and session(maybe only single storage has it)
     * or generate and save new if not found
     *
     * @author Bogdan Medvedev <bogdan.medvedev@tstechpro.com>
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return int|mixed|null
     */
    public static function initNotAuthedUID()
    {

        $keyName = 'notAuthedUID';

        /** validate this value. it must be int. if no - regenerate it */
        $notAuthedUID = (self::arrGet($_SESSION, $keyName))
            ?
            : Cookie::get($keyName);

        if ($notAuthedUID && !ctype_digit($notAuthedUID)) {
            unset($_SESSION[$keyName]);
            Cookie::delete($keyName);
            $notAuthedUID = null;
        }

        if (!$notAuthedUID) {

            $notAuthedUID = rand(500000000,999999999);
        }

        //set time for cookies 1 year
        $cookieTime = time() + 60 * 60 * 24 * 365;

        $_SESSION[$keyName] = $notAuthedUID;
        Cookie::set(
            $keyName,
            $notAuthedUID,
            $cookieTime,
            '/',
            self::getMainDomain()
        );

        return $notAuthedUID;
    }

    /**
     * show lead pixels process  from stats server
     * print pixels in content
     *
     * @author Roman Lazarskiy <roman.lazarskiy@tstechpro.com>
     *
     * @return void
     */
    public static function initPixels()
    {
        $stats = new tradersoft\model\Stats();

        /** we dont need show pixels when js redirect page */
        add_action('tradersoft/before_redirect', function() use ($stats) {
            $stats->ignorePixels();
        });

        /** show pixels before footer */
        add_action('get_footer', function() use ($stats) {
            echo $stats->getPixelsContent();
        });
    }


    public static function isAjax()
    {
        return isset($_POST['ajax']);
    }

    /**
     * Ajax template include
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     */
    public static function ajax_template_include()
    {
        add_filter(
            'template_include',
            function ()
            {
                return self::loadTemplate('system/ajax');
            }
        );
    }

    /**
     * @deprecated Use \tradersoft\helpers\Config::get()
     * Load config
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param string $configPath
     * @return array
     */
    public static function loadConfig($configPath)
    {
        if (!(Arr::get(self::$cache, $configPath))) {
            self::$cache[$configPath] = require dirname(__FILE__) . '/../configs/' . $configPath .'.php';
        }

        return self::$cache[$configPath];
    }

    /**
     * Load template
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param $templatePath
     * @param array $vars
     * @return string
     */
    public static function loadTemplate($templatePath, $vars = [])
    {
        self::$_templatePath = dirname(__FILE__) . '/../templates/' . $templatePath .'.php';

        if (!file_exists(self::$_templatePath)) {
            return '';
        }

        if ($vars) {
            extract($vars);
        }

        ob_start();
        require self::$_templatePath;
        $template = ob_get_contents();
        ob_end_clean();

        return $template;
    }

    /**
     * Function translateByKey
     * Try to get value from meta values of current post and translate it
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $key
     * @param string $domain
     * @param bool $keyIfEmpty return key as value if value is empty
     *
     * @return string
     */
    public static function translateByKey($key, $domain = 'default', $keyIfEmpty = true)
    {

        $keyFullName = "{$domain}_{$key}";

        $metaValue = get_post_meta(get_the_ID(), $keyFullName, true);

        /** If no key in terms found - just add it as new*/
        if (!$metaValue) {

            add_post_meta(get_the_ID(), $keyFullName, '', true);

            if ($keyIfEmpty) {
                $metaValue = $key;
            }
        }

        return (string) $metaValue;

    }

    /**
     * @deprecated  use \tradersoft\helpers\Platform::getLinks()
     * Get platform links
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return array $result
     */
    public static function getPlatformLinks()
    {
        return Platform::getLinks();
    }

    /**
     * @return string
     */
    public static function getCurrentUrl()
    {
        return ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * @deprecated use \tradersoft\helpers\system\Translate::__()
     * @param $text
     *
     * @return string|void
     */
    public static function __($text)
    {
        return __($text, Multi_Language::getInstance()->getTextDomain());
    }

    public static function _e($text)
    {
        return _e($text, Multi_Language::getInstance()->getTextDomain());
    }
}