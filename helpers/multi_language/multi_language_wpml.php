<?php

namespace tradersoft\helpers\multi_language;

/**
 * Class for multi language implementation by WPML plugin
 * @package tradersoft\helpers\multi_language
 */
class Multi_Language_WPML implements Multi_Language_Interface
{
    const TEXT_DOMAIN = 'default';

    /**
     * @var object Main SitePress Class instance
     */
    protected $_sitePress;

    /**
     * Multi_Language_WPML constructor.
     * @param string $pluginPath
     */
    public function __construct($pluginPath)
    {
        global $sitepress;
        $this->_sitePress = $sitepress;

        add_filter('wpml_is_redirected', [$this, 'addParametersToRedirectUrl'], 20, 1);
    }

    /**
     * @inheritdoc
     */
    public function getHomeUrl() {
        return icl_get_home_url();
    }

    /**
     * @inheritdoc
     */
    public function getActiveLanguages() {
        return $this->_sitePress->get_active_languages();
    }

    /**
     * @inheritdoc
     */
    public function getCurrentLanguage() {
        return $this->_sitePress->get_current_language();
    }

    /**
     * @inheritdoc
     */
    public function switchLang($code, $cookieLang) {
        return $this->_sitePress->switch_lang($code, $cookieLang);
    }

    /**
     * @param false|string $redirect
     *
     * @return string|false
     */
    public function addParametersToRedirectUrl($redirect)
    {
        if ($redirect) {
            $urlParts = explode('?', $_SERVER['REQUEST_URI']);

            if (isset($urlParts[1])) {
                $urlParameters = [];

                parse_str($urlParts[1], $urlParameters);

                foreach ($urlParameters as $key => $parameter) {
                    $redirect = add_query_arg([$key => $parameter], $redirect);
                }
            }
        }

        return $redirect;
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
        if ($message == '') {
            return '';
        }

        $messageKey = md5($message);

        if (
            !function_exists('icl_st_is_registered_string')
            || !icl_st_is_registered_string($this->getTextDomain(), $messageKey)
        ) {
            do_action( 'wpml_register_single_string', $this->getTextDomain(), $messageKey, $message);
        }

        return apply_filters('wpml_translate_single_string', $message, $this->getTextDomain(), $messageKey);
    }
}