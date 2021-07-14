<?php

namespace tradersoft\helpers\multi_language;

use WPM\Includes\WP_Multilang;

/**
 * Class for multi language implementation by WPM plugin
 * @package tradersoft\helpers\multi_language
 */
class Multi_Language_WPM implements Multi_Language_Interface
{
    const TEXT_DOMAIN = 'tradersoft';
    private $_wpm;

    /**
     * Multi_Language_WPM constructor.
     * @param string $path
     */
    public function __construct($path)
    {
        $this->_loadPlugin($path);
        $this->_initPluginTextDomain();

        $this->_wpm = WP_Multilang::instance();
    }

    /**
     *  Include WPM plugin entry point
     *
     * @param string $path
     */
    private function _loadPlugin($path)
    {
        $pluginLanguagePath = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $path;
        include_once $pluginLanguagePath;
    }

    /**
     *  Initialization localization text domain and set localization files path
     */
    private function _initPluginTextDomain()
    {
        add_action('init', function () {
            $pluginLanguagePath = WP_CONTENT_DIR . '/languages/plugins';

            if ($pluginLanguagePath) {
                load_plugin_textdomain(static::TEXT_DOMAIN, false, $pluginLanguagePath);
            }
        });
    }

    /**
     * @inheritdoc
     */
    public function getHomeUrl()
    {
        $defaultLanguage = wpm_get_default_language();
        $currentLanguage = $this->getCurrentLanguage();

        return ($defaultLanguage != $currentLanguage)
            ? wpm_get_orig_home_url() . '/' . $currentLanguage . '/'
            : wpm_get_orig_home_url() . '/';
    }

    /**
     * @inheritdoc
     */
    public function getActiveLanguages()
    {

        $languages = $this->_wpm->setup->get_languages();
        $result = [];

        foreach ($languages as $code => $data) {
            $result[$code] = [
                'code' => $code,
                'id' => '0',
                'english_name' => $data['name'],
                'native_name' => $data['name'],
                'major' => '1',
                'active' => '1',
                'default_locale' => $data['locale'],
                'encode_url' => '0',
                'tag' => $code,
                'display_name' => $data['name'],
            ];
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentLanguage()
    {
        return $this->_wpm->setup->get_user_language();
    }

    /**
     * @inheritdoc
     */
    public function switchLang($code, $cookieLang)
    {
        if ($this->getCurrentLanguage() == $code
            || !array_key_exists($code, $this->getActiveLanguages())) {
            return;
        }

        $redirectUrl = wpm_translate_current_url($code);

        wp_redirect($redirectUrl);
        exit;
    }

    /**
     * @inheritdoc
     */
    public function getTextDomain()
    {
        return static::TEXT_DOMAIN;
    }

    /**
     * @inheritdoc
     */
    public function translate($message)
    {
        return __($message, $this->getTextDomain());
    }
}
