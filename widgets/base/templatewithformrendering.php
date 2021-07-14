<?php
namespace tradersoft\widgets\base;

/**
 * Functional for rendering arbitrary  form templates
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
trait TemplateWithFormRendering
{
    use TemplateRendering {
        _validateTemplate as _validateTemplateParent;
        _rendering as _renderingParent;
    }

    public $templateModelName = 'model';

    protected $_formStartTag = '[form-start]';
    protected $_formEndTag = '[form-end]';
    protected $_formOptions = [];
    protected $_templateFormKeys = [];

    /**
     * @return array
     * For example
     * ```php
     * return [
     *       '[email]' => [
     *           'field' => 'email',
     *           'fieldOptions' => ['key'=>'val'],
     *           'input' => 'textInput',
     *           'inputOptions' =>['key'=>'val'],
     *           'label' => 'label',
     *           'labelOptions' => ['key'=>'val'],
     *           'htmlBlock' => 'html',
     *       ],
     *       '[password]' => [
     *           'field' => 'password',
     *           'input' => 'passwordInput'
     *       ]
     *   ];
     * ```
     */
    public abstract function formField();

    /**
     * @return \tradersoft\model\Model
     */
    public abstract function getModel();

    /**
     * Rendering template
     * @return bool
     */
    protected function _rendering()
    {
        $fields = $this->formField();
        $template = $this->templateContent;

        $this->_formStart($template);
        foreach ($this->_templateFormKeys as $key => $field) {
            $this->_renderFieldForm($template, $field, $fields[$field]);
        }
        $this->_formEnd($template);

        $this->templateContent = $template;
        return $this->_renderingParent();
    }

    /**
     * Before render template
     */
    protected function _beforeRenderTemplate()
    {
        $this->addTemplateVar($this->_getTemplateModelName(), $this->getModel());
    }

    /**
     * Validate form field
     * @return bool
     */
    protected function _validateTemplate()
    {
        if (!$this->_validateTemplateParent()) {
            return false;
        }

        $posFormStart = strpos($this->templateContent, $this->_formStartTag);
        $posFormEnd = strpos($this->templateContent, $this->_formEndTag);
        if($posFormStart === false || $posFormEnd === false) {
            return false;
        }

        $fields = $this->formField();
        if (empty($fields) || !is_array($fields)) {
            return false;
        }

        preg_match_all( '/(\[[a-z-]+\])/', $this->templateContent, $matches );
        if (empty($matches[1])) {
            return false;
        }
        $keys = array_diff($matches[1],[$this->_formStartTag,$this->_formEndTag]);
        if (empty($keys)) {
            return false;
        }

        $fieldKeys = array_keys($fields);
        foreach ($keys as $key => $field) {
            $posField = strpos($this->templateContent, $field);
            if (!in_array($field, $fieldKeys)) {
                unset($keys[$key]);
                continue;
            }
            if ($posField < $posFormStart || $posField > $posFormEnd) {
                return false;
            }
        }
        $this->_templateFormKeys = $keys;

        return true;
    }

    /**
     * @return string
     */
    private function _getTemplateModelName()
    {
        return !empty($this->templateModelName) ? $this->templateModelName : 'model';
    }

    /**
     * Rendering field
     * @param $template string
     * @param $field string
     * @param $fieldOptions array
     */
    private function _renderFieldForm(&$template, $field, $fieldOptions)
    {
        if ($field=='[button]') {
            $this->_renderButton($template, $field, $fieldOptions);
            return;
        }
        if (empty($fieldOptions['field'])) {
            return;
        }

        $fieldOpt = \tradersoft\helpers\Arr::get($fieldOptions, 'fieldOptions', []);

        $code = '<?php';
        $code .= ' echo $form->field(\'' . $fieldOptions['field'] . '\', ' . var_export($fieldOpt, true) . ')';
        if (!empty($fieldOptions['input'])) {
            $inputOpt = \tradersoft\helpers\Arr::get($fieldOptions, 'inputOptions', []);
            $items = !empty($fieldOptions['items']) ? var_export($fieldOptions['items'], true) . ', ' : '';
            $code .= '->' . $fieldOptions['input'] . '(' . ($items ? $items : '') . var_export($inputOpt, true) . ')';
        }
        if (!empty($fieldOptions['label'])) {
            $labelOpt = \tradersoft\helpers\Arr::get($fieldOptions, 'labelOptions', []);
            $code .= '->label(\'' . $fieldOptions['label'] . '\', ' . var_export($labelOpt, true) . ')';
        }
        if (!empty($fieldOptions['htmlBlock'])) {
            $code .= '->htmlBlock(\'' . $fieldOptions['htmlBlock'] . '\')';
        }
        $code .= ';?>';

        $template = str_replace($field, $code, $template);
    }

    /**
     * Rendering button
     * @param $template string
     * @param $field string
     * @param $fieldOptions array
     */
    private function _renderButton(&$template, $field, $fieldOptions)
    {
        $label = \tradersoft\helpers\Arr::get($fieldOptions, 'label', '');
        $options = \tradersoft\helpers\Arr::get($fieldOptions, 'options', []);
        $code = '<?php echo \tradersoft\helpers\Html::submitInput(\'' . $label . '\', ' . var_export($options, true) . ');?>';

        $template = str_replace($field, $code, $template);
    }

    /**
     * Start form
     * @param $template string
     */
    private function _formStart(&$template)
    {
        $className = mb_strtolower(get_class($this->getModel()));
        $code = "\n" . '<?php $' . $this->_getTemplateModelName() . ' = TSInit::$app->getVar(\'' . $this->_getTemplateModelName() . '\'); ?>'. "\n";
        $code .= '<?php $form = \tradersoft\helpers\Form::begin($' . $this->_getTemplateModelName() . ', ' . var_export($this->_formOptions, true) . ')?>'. "\n";
        $code .= '<?php echo \tradersoft\helpers\Html::hiddenInput(\'tradersoft_submit\', \'' . (substr($className, strrpos($className, '\\') + 1)) . '\'); ?>'. "\n";
        $template = str_replace($this->_formStartTag, $code, $template);
    }

    /**
     * End form
     * @param $template string
     */
    private function _formEnd(&$template)
    {
        $template = str_replace($this->_formEndTag, "\n" . '<?php \tradersoft\helpers\Form::end()?>' . "\n", $template);
    }
}