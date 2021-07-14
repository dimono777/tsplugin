<?php
namespace tradersoft\helpers;

use tradersoft\model\Media_Queue;

/**
 * Widget
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Widget
{
    public static $stack = [];

    public function run()
    {}

    /**
     * @param array $config
     * @return static
     */
    protected static function _begin($config = [])
    {
        $widget = new static($config);
        static::$stack[] = $widget;

        return $widget;
    }

    protected static function _end()
    {
        $widget = null;
        if (!empty(static::$stack)) {
            /* @var $widget Widget */
            $widget = array_pop(static::$stack);
            $widget->run();
            Media_Queue::getInstanceByInitiatorOnly(get_class($widget))->enqueue();
        }
        return $widget;
    }

    /**
     * Function _jsOnJQueryRady
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $js
     * @param $relatedWith
     *
     * @return void
     */
    protected function _jsOnJQueryRady($js, $relatedWith)
    {
        Media_Queue::getInstanceByInitiatorOnly(get_class($this))->addScriptInline(
            sprintf(
                'jQuery(document).ready(function () { %s });',
                $js
            ),
            $relatedWith
        );
    }

    /**
     * Function _addScript
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param string $source
     * @param string|null $themeContainer
     * @param bool $version
     * @param string $uniqueName
     * @param array $dependsOn
     * @param bool $inFooter
     *
     * @return void
     */
    protected function _addScript($source, $themeContainer = null, $version = false, $uniqueName = '', array $dependsOn = [], $inFooter = true)
    {
        Media_Queue::getInstanceByInitiatorOnly(get_class($this))
            ->addScript($source, $themeContainer, $version, $uniqueName, $dependsOn, $inFooter);

    }

    /**
     * Function _addStyle
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param string $source
     * @param string|null $themeContainer
     * @param bool $version
     * @param string $uniqueName
     * @param array $dependsOn
     *
     * @param bool $inFooter
     *
     * @return void
     */
    protected function _addStyle(
        $source,
        $themeContainer = null,
        $version = false,
        $uniqueName = '',
        array $dependsOn = [],
        $inFooter = true
    ) {
        Media_Queue::getInstanceByInitiatorOnly(get_class($this))
            ->addStyle($source, $themeContainer, $version, $uniqueName, $dependsOn, $inFooter);
    }
}