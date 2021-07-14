<?php

namespace tradersoft;


class View
{

    private static $_content = [];

    /**
     * @param $path
     * @param $expression
     */
    public static function render($path, $expression)
    {
        self::$_content[$expression] = self::load($path);
    }

    public static function setContent($content, $expression)
    {
        self::$_content[$expression] = $content;
    }

    /**
     * Function load
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $path
     * @param array $params
     *
     * @return string
     */
    public static function load($viewFile, $params = [])
    {

        if (!$viewFile) {
            return '';
        }

        $fullPath = '';

        foreach (\tradersoft\helpers\Assets::getContainersList() as $path => $url) {

            $fullPath =
                rtrim($path, '/')
                . '/'
                . trim($viewFile, '/')
                . '.php';

            /** File exists - stop checking of rest containers */
            if (file_exists($fullPath)) {
                break;
            }

            /** File doesn't exists - clear its address */
            $fullPath = '';
        }

        if (!$fullPath) {
            return '';
        }


        ob_start();
        ob_implicit_flush(false);
        extract($params, EXTR_OVERWRITE);
            require $fullPath;
        return ob_get_clean();

    }

    /**
     * DO NOT TOUCH!!!
     * Called by WP Hook theContent. Replace short codes with rendered content
     * @param $content string
     * @return string
     */
    public static function theContent($content)
    {
        if (empty($content)) {
            return $content;
        }

        foreach (self::$_content as $expression => $replaceContent) {
            $content = str_replace($expression, $replaceContent, $content);
        }

        return $content;
    }
}