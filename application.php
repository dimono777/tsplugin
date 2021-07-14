<?php
namespace tradersoft;

use tradersoft\helpers\Session;
use tradersoft\helpers\Request;
use tradersoft\model\Trader;

/**
 * Base class for application
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Application
{
    /** @var $session Session */
    public $session;
    /** @var $request Request */
    public $request;
    /** @var $trader Trader */
    public $trader;

    private $vars = [];
    private static $_instance = null;

    /**
     * Set vars
     * @param $vars array
     */
    public function setVars(array $vars)
    {
        $this->vars = array_merge($this->vars, $vars);
    }

    /**
     * Set var
     * @param $name string
     * @param $value mixed
     */
    public function setVar($name, $value)
    {
        $this->vars[$name] = $value;
    }

    /**
     * Get vars
     * @return mixed
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * Get var
     * @param $name string
     * @param $default mixed
     * @return mixed
     */
    public function getVar($name, $default = null)
    {
        if (isset($this->vars[$name])) {
            return $this->vars[$name];
        }
        return $default;
    }

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function initTrader()
    {
        $this->trader = Trader::getInstance();
    }

    private function __construct()
    {
        $this->_init();
    }

    private function __clone()
    {}

    private function __wakeup()
    {}

    private function _init()
    {
        $this->session =  new Session();
        $this->session->open();

        $this->request = new Request();
    }
}