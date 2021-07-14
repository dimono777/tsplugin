<?php
namespace tradersoft\helpers\system;

use tradersoft\helpers\multi_language\Multi_Language;

class Translate
{
    /**
     * Translate::__('Hello, :user', [':user' => $username]);
     *
     * @param $text string
     * @param $params array
     * @return string
     */
    public static function __($text, array $params = [])
    {
        $text = Multi_Language::getInstance()->translate($text);
        return empty($params) ? $text : strtr($text, $params);
    }
}