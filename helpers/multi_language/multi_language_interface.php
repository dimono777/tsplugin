<?php

namespace tradersoft\helpers\multi_language;

/**
 * Multi language interface
 * Driver for multi language plugin must implements this interface
 * @package tradersoft\helpers\multi_language
 */
interface Multi_Language_Interface
{
    /**
     * Return home url
     *
     * @return string
     */
    public function getHomeUrl();

    /**
     * Return active languages
     *
     * @return array
     */
    public function getActiveLanguages();

    /**
     * Return current language
     *
     * @return string
     */
    public function getCurrentLanguage();

    /**
     * Switches whole site to the given language or back to the current language
     * that was set when first calling this function.
     *
     * @param null|string $code language code to switch into, will revert to
     * initial language if null is given
     * @param bool|string $cookieLang optionally also switch the cookie language
     * to the value given
     */
    public function switchLang($code, $cookieLang);

    /**
     * Return current plugin language text domain
     *
     * @return string
     */
    public function getTextDomain();

    /**
     * @param string $message
     *
     * @return string
     */
    public function translate($message);
}