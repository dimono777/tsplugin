<?php
namespace tradersoft\helpers;

use tradersoft\controllers\Base_Controller;
use tradersoft\helpers\multi_language\Multi_Language;

/**
 * Request
 * @property string $homeUrl Home url
 * @property string $hostInfo Host info
 * @property string $hostName Host name
 * @property string $path Request path
 * @property string $pathBase Request base path
 * @property int $port Port
 * @property int $securePort Port
 * @property string $method Request method
 * @property string $userIP User ip address
 * @property bool $isLocal
 *
 * @property bool $isSecureConnection If is https. Read-only.
 * @property bool $isAjax XMLHttpRequest request. Read-only.
 * @property bool $isPost POST request. Read-only.
 * @property bool $isGet GET request. Read-only.
 *
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Request
{
    protected $_headers = [];
    protected $_hostInfo;
    protected $_hostName;
    protected $_path;
    protected $_pathBase;
    protected $_securePort;
    protected $_port;
    protected $_controllers = [];
    private $ml;

    public function __construct()
    {
        $this->ml = Multi_Language::getInstance();
    }

    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        throw new \Exception('Getting unknown property: ' . get_class($this) . '::' . $name);
    }

    /**
     * Returns  HTTP headers.
     * @return array
     */
    public function getHeaders()
    {
        if (empty($this->_headers)) {
            if (function_exists('getallheaders')) {
                $this->_headers = getallheaders();
            } else {
                foreach ($_SERVER as $name => $value) {
                    if (strncmp($name, 'HTTP_', 5) === 0) {
                        $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                        $this->_headers[$name] = $value;
                    }
                }
            }
        }

        return $this->_headers;
    }

    /**
     * Function addController
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param Base_Controller $controller
     *
     * @return void
     */
    public function addController(Base_Controller $controller)
    {

        $this->_controllers[] = $controller;
    }

    /**
     * Function Controllers
     * Get _controllers value
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @return array
     */
    public function getControllers()
    {

        return $this->_controllers;
    }

    /**
     * @return string request method, such as GET, POST, HEAD, PUT, PATCH, DELETE.
     */
    public function getMethod()
    {
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            return strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        }

        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }

        return 'GET';
    }

    /**
     * This request is GET.
     * @return bool.
     */
    public function getIsGet()
    {
        return $this->getMethod() === 'GET';
    }

    /**
     * This request is POST.
     * @return bool.
     */
    public function getIsPost()
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * This request is XMLHttpRequest.
     * @return bool.
     */
    public function getIsAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * Returns POST parameter.
     *
     * @param string $name
     * @param mixed $default.
     * @return array|mixed
     */
    public function post($name = null, $default = null)
    {
        if (is_null($name)) {
            return $_POST;
        } else {
            return Arr::get($_POST, $name, $default);
        }
    }

    /**
     * Returns GET parameter.
     *
     * @param string $name
     * @param mixed $default.
     * @return array|mixed
     */
    public function get($name = null, $default = null)
    {
        if (is_null($name)) {
            return $_GET;
        } else {
            return Arr::get($_GET, $name, $default);
        }
    }

    /**
     * Get host info
     * @return string
     */
    public function getHostInfo()
    {
        if (is_null($this->_hostInfo)) {
            $secure = self::getIsSecureConnection();
            $http = $secure ? 'https' : 'http';
            if (isset($_SERVER['HTTP_HOST'])) {
                $this->_hostInfo = $http . '://' . $_SERVER['HTTP_HOST'];
            } elseif (isset($_SERVER['SERVER_NAME'])) {
                $this->_hostInfo = $http . '://' . $_SERVER['SERVER_NAME'];
                $port = $secure ? $this->getSecurePort() : $this->getPort();
                if (($port !== 80 && !$secure) || ($port !== 443 && $secure)) {
                    $this->_hostInfo .= ':' . $port;
                }
            }
        }

        return $this->_hostInfo;
    }

    /**
     * Get host name
     * @return string
     */
    public function getHostName()
    {
        if (is_null($this->_hostName)) {
            $this->_hostName = parse_url($this->getHostInfo(), PHP_URL_HOST);
        }

        return $this->_hostName;
    }

    /**
     * Get host name by step level
     * @return string|null
     */
    public function getMainDomain($step = null) {
        $parts = explode('.', $this->hostName);
        $count = sizeof($parts);
        if ($step === null) {
            $step = \TS_Functions::getCookiesDomainLevel();
        }
        if ($count >= $step) {
            $return = array();
            while ($step > 0) {
                $return[] = $parts[$count - $step];
                $step--;
            }

            return implode('.', $return);
        }
        return null;
    }

    /**
     * Returns the port request.
     * Default 80
     * @return int
     */
    public function getPort()
    {
        if (is_null($this->_port)) {
            $this->_port = !self::getIsSecureConnection() && isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : 80;
        }

        return $this->_port;
    }

    /**
     * Returns the port secure request.
     * Default 443
     * @return int
     */
    public function getSecurePort()
    {
        if (is_null($this->_securePort)) {
            $this->_securePort = self::getIsSecureConnection() && isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : 443;
        }

        return $this->_securePort;
    }

    /**
     * Returns currently request URI
     * @return string
     */
    public function getPath()
    {
        if (is_null($this->_path)) {
            if (isset($_SERVER['REQUEST_URI'])) {
                $this->_path = Link::sanitizeURI($_SERVER['REQUEST_URI']);
            }
        }

        return $this->_path;
    }

    /**
     * Returns currently request URL
     * @return string
     */
    public function getPathBase()
    {
        if (is_null($this->_pathBase)) {
            $this->_pathBase = get_permalink();
        }

        return $this->_pathBase;
    }

    /**
     * Get full link
     * @param $url string
     * @return string
     */
    public function getLink($url)
    {
        return $this->getHomeUrl() . ltrim($url, '/');
    }

    /**
     * Get home url
     * @return string
     */
    public function getHomeUrl()
    {
        return $this->ml->getHomeUrl();
    }

    /**
     * Get referer url
     * @return string|null
     */
    public function getReferer()
    {
        return Arr::get($_SERVER, 'HTTP_REFERER');
    }

    /**
     * Redirect to URL
     * @param $url string
     */
    public function redirect($url)
    {
        if (strpos($url, '/') === 0 && strpos($url, '//') !== 0) {
            $url = $this->getLink($url);
        }

        do_action('tradersoft/before_redirect');
        header("Location: " . $url);
        exit;
    }

    /**
     * Redirect to URL
     */
    public function refresh()
    {
        do_action('tradersoft/before_redirect');
        header('Refresh: 0;');
        exit;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return null|string
     */
    public function getUserIP()
    {
        return Ip::getCurrent() ?: '';
    }

    /**
     * @return bool
     */
    public static function getIsLocal()
    {
        return strpos($_SERVER['HTTP_HOST'], '.local') > 0;
    }

    /**
     * Return request is https.
     * @return bool
     */
    public static function getIsSecureConnection()
    {
        return isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1)
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
    }
}