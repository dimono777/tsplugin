<?php
namespace tradersoft\widgets\base;

use tradersoft\model\Media_Queue;

/**
 * Functional for rendering arbitrary templates
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
trait TemplateRendering {

    public $template;
    public $templateContent;
    public $defaultTemplate;
    protected $_templateKeys = [];
    private $_templateVars = [];

    public function addTemplateVar($key, $value)
    {
        if (!isset($this->_templateVars[$key])) {
            $this->_templateVars[$key] = $value;
        }
    }

    /**
     * Return template field
     * @return array
     */
    public function templateFields()
    {
        return [];
    }

    /**
     * @param $args array
     * @param $instance array
     */
    protected function _prepareArgs($args, $instance)
    {
        if (isset($instance['template']) && file_exists($instance['template'])) {
            $this->template = $instance['template'];
        }
        if(!empty($instance['js'])) {
            foreach ($instance['js'] as $js) {
                $this->_addScript($js);
            }
        }
        if(!empty($instance['css'])) {
            foreach ($instance['css'] as $css) {
                $this->_addStyle($css);
            }
        }
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

    protected function _beforeRenderTemplate()
    {}

    protected function _renderTemplate()
    {
        $this->_beforeRenderTemplate();
        ob_start();
        ob_implicit_flush(false);
        $this->_renderStatic();
        $this->_getContent();
        $template = ob_get_clean();
        echo $template;
    }

    protected function _renderStatic()
    {
        Media_Queue::getInstanceByInitiatorOnly(get_class($this))->enqueue();
    }

    protected function _setTemplateVars()
    {
        \TSInit::$app->setVars($this->_templateVars);
    }

    protected function _getContent()
    {
        $this->_setTemplateVars();

        if (empty($this->template) && ($defaultTemplate = $this->_getDefaultTemplate()) != false) {
            require $defaultTemplate;
            return;
        }

        $renderingTemplate = $this->_getRenderingTemplate();
        if (!$renderingTemplate) {
            $this->_getTemplateContent();
            if (!$this->_validateTemplate()) {
                return;
            }
            if (!$this->_rendering()) {
                return;
            }
            $renderingTemplate = $this->_getRenderingTemplate();
        }

        if ($renderingTemplate) {
            require $renderingTemplate;
        }
    }

    /**
     * Validate template
     * @return bool
     */
    protected function _validateTemplate()
    {
        return true;
    }

    /**
     * Rendering template
     * @return bool
     */
    protected function _rendering()
    {
        $template = $this->templateContent;
        $this->_searchTemplateKeys();
        $fields = $this->templateFields();

        foreach ($this->_templateKeys as $field) {
            if (isset($fields[$field])) {
                $this->_renderField($template, $field, $fields[$field]);
            }
        }

        return $this->_saveRenderingTemplate($template);
    }

    protected function _searchTemplateKeys()
    {
        preg_match_all('/(\[[a-z-]+\])/', $this->templateContent, $matches);
        $this->_templateKeys = \tradersoft\helpers\Arr::get($matches, 0, []);
    }

    /**
     * Rendering field
     * @param $template string
     * @param $field string
     * @param $fieldMethod string
     */
    private function _renderField(&$template, $field, $fieldMethod)
    {
        if (method_exists($this, $fieldMethod)) {
            $template = str_replace($field, $this->{$fieldMethod}(), $template);
        }
    }

    /**
     * @return string|bool
     */
    private function _getDefaultTemplate()
    {
        if (!empty($this->defaultTemplate) && file_exists($this->defaultTemplate)) {
            return $this->defaultTemplate;
        }
        $template = TS_DOCROOT . DIRECTORY_SEPARATOR . 'widgets' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $this->_getClassName() . DIRECTORY_SEPARATOR . 'default.php';
        if (file_exists($template)) {
            return $template;
        }
        return false;
    }

    /**
     * @return string|bool
     */
    private function _getRenderingTemplate()
    {
        $template = $this->_getRenderingTemplatePath();
        if (file_exists($template) && file_exists($this->template) && filemtime($template)> filemtime($this->template)) {
            return $template;
        }

        return false;
    }

    /**
     * @return string
     */
    private function _getRenderingTemplatePath()
    {
        return TS_DOCROOT . 'runtime' . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . $this->_getClassName(). '_' . basename($this->template, '.php') . '.php';
    }

    private function _getTemplateContent()
    {
        $this->templateContent = file_get_contents($this->template);
    }

    /**
     * Save rendering template
     */
    private function _saveRenderingTemplate($text)
    {
        $template = $this->_getRenderingTemplatePath();
        if (!is_dir(TS_DOCROOT . 'runtime' . DIRECTORY_SEPARATOR . 'html')) {
            mkdir(TS_DOCROOT . 'runtime' . DIRECTORY_SEPARATOR . 'html', 0700, true);
        }

        return file_put_contents($template, $text) !== false;

    }
}