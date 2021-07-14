<?php

namespace tradersoft\helpers\multi_language;

use tradersoft\helpers\Config;

/**
 * Class Multi_Language
 * @package tradersoft\helpers\multi_language
 */
class Multi_Language implements Multi_Language_Interface
{
    /**
     * @var Multi_Language_Interface $_driverML
     */
    protected $_driverML;

    /**
     * Get Multi_Language instance
     *
     * @return static
     *
     * @author Alexander Shpak <alexander.shpak@tstechpro.com>
     */
    public static function getInstance()
    {
        static $instance = null;
        if (is_null($instance)) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * @inheritdoc
     */
    public function getHomeUrl()
    {
        return $this->_driverML->getHomeUrl();
    }

    /**
     * @inheritdoc
     */
    public function getActiveLanguages() {
        return $this->_driverML->getActiveLanguages();
    }

    /**
     * @inheritdoc
     */
    public function getCurrentLanguage() {
        return $this->_driverML->getCurrentLanguage();
    }

    /**
     * @inheritdoc
     */
    public function switchLang($code = null, $cookieLang = false) {
        return $this->_driverML->switchLang($code, $cookieLang);
    }

    /**
     * @inheritdoc
     */
    public function getTextDomain()
    {
        return $this->_driverML->getTextDomain();
    }

    /**
     * @inheritdoc
     */
    public function translate($message)
    {
        return $this->_driverML->translate($message);
    }

    /**
     * Multi_Language constructor
     */
    public function __construct()
    {
        // require file with standard WP plugin functions to use it later
        if (!function_exists('is_plugin_active')) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }

        // check which of available multi language plugins are enabled
        // and get exact plugin's helper class name
        $multiLanguagePlugins = Config::get('multi_language_plugins', []);
        $activePluginClass = null;
        $pluginFolder = null;

        foreach ($multiLanguagePlugins as $pathToPlugin => $pluginHelperClass) {
            if (is_plugin_active($pathToPlugin)) {
                $activePluginClass = $pluginHelperClass;
                $pluginFolder = $pathToPlugin;
                break;
            }
        }

        $this->_driverML = (empty($activePluginClass))
            ? new Multi_Language_Default()
            : new $activePluginClass($pluginFolder);
    }
}