<?php

namespace tradersoft\helpers\multi_language;

/**
 * Class for multi language implementation by default (if there is no enabled plugin)
 * @package tradersoft\helpers\multi_language
 */
class Multi_Language_Default implements Multi_Language_Interface
{
    const TEXT_DOMAIN = '';
    /**
     * @inheritdoc
     */
    public function getHomeUrl() {
        $url = \TSInit::$app->request->getHostInfo();
        return rtrim($url, '/') . '/';
    }

    /**
     * @inheritdoc
     */
    public function getActiveLanguages() {
        // return default language
        return [
            'en' => [
                'code' => 'en',
                'id' => '1',
                'english_name' => 'English',
                'native_name' => 'English',
                'major' => '1',
                'active' => '1',
                'default_locale' => 'en_US',
                'encode_url' => '0',
                'tag' => 'en',
                'display_name' => 'English'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getCurrentLanguage() {
        // return default language's code
        return 'en';
    }

    /**
     * @inheritdoc
     */
    public function switchLang($code, $cookieLang) {
        // there is nothing to do because only one language is enabled
        return true;
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