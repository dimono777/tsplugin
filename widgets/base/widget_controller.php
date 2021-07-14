<?php

namespace tradersoft\widgets\base;

use tradersoft\helpers\Config;
use tradersoft\helpers\System\PageKey;
use tradersoft\helpers\Arr;
use tradersoft\Router;
use TSInit;
use tradersoft\controllers\Base_Controller;

/**
 * Base widget.
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
abstract class Widget_Controller extends Widget
{
    const WRAPPER_KEY = '{{WIDGET_CONTENT}}';

    private $_shortCodeParams = [];
    
    abstract public function getShortCode();

    /**
     * Display the widget
     */
    protected function _widget($args, $instance)
    {
        $this->_setShortCodeParams(Arr::get($instance, 'shortCodeParams', []));

        if (!$controller = $this->_initController()) {
            return false;
        }

        $controller->execute();

        parent::_widget($args, $instance);

        $content = $this->_getRenderingContent($controller);
        if (!empty($instance['wrapper'])) {
            $content = str_replace(self::WRAPPER_KEY, $content, $instance['wrapper']);
        }
        echo $content;
    }

    /**
     * @param Base_Controller $controller
     * @return string
     */
    protected function _getRenderingContent(Base_Controller $controller)
    {
        return $this->_render($controller->view);
    }

    /**
     * Initialization controller
     * @return Base_Controller|false
     */
    protected function _initController()
    {
        $shortCode = $this->getShortCode();

        // Initialization controller
        $controller = $this->_initControllerByShortCode($shortCode);

        // Check access
        if (!$this->_access()) {
            return false;
        }
        $keys = PageKey::getPagesActions();
        if (!array_key_exists($shortCode, $keys)) {
            return false;
        }

        return $controller;
    }

    /**
     * Initialization controller by short code
     * @param string $shortCode
     * @return Base_Controller|false
     */
    protected function _initControllerByShortCode($shortCode)
    {
        if (is_admin() || !$this->_access()) {
            return false;
        };

        try {
            /** @var Base_Controller $controller */
            $controller = Router::loadControllerByShortCode($shortCode)
                ->setParams($this->_getShortCodeParams())
                ->setView($this->_getTemplateByKey($shortCode));

        } catch (\Exception $e) {
            wp_die($e->getMessage());
        }

        return $controller;
    }

    /**
     * @param $key string
     * @param $defaultTemplate string|null
     * @return string
     */
    protected function _getTemplateByKey($key, $defaultTemplate = '')
    {
        $keys = PageKey::getPagesActions();
        if (empty($keys[$key])) {
            return $defaultTemplate;
        }

        return $keys[$key];
    }

    /**
     * Set short code params
     * @param $params array
     */
    protected function _setShortCodeParams(array $params)
    {
        $this->_shortCodeParams = $params;
    }

    protected function _getShortCodeParams()
    {
        if (!is_array($this->_shortCodeParams)) {
            return [];
        }

        return $this->_shortCodeParams;
    }
}