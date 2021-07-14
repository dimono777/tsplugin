<?php
namespace tradersoft\helpers;

use Exception;
use tradersoft\helpers\abstracts\BasicForm;
use tradersoft\helpers\captcha\Captcha_Abstract;
use tradersoft\helpers\captcha\Invisible_ReCaptcha;
use tradersoft\helpers\captcha\ReCaptcha;
use tradersoft\model\form\fields\ActiveField;
use tradersoft\model\Model;
use tradersoft\model\ModelWithFieldInterface;
use tradersoft\model\validator\Not_Empty;
use tradersoft\model\validator\Required;
use tradersoft\model\validator\UniqueGlobal;
use tradersoft\model\validator\Validator;

/**
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Field
{
    const OPTION_PHONE_CODE = 'phoneCode';
    const OPTION_PHONE_NUMBER = 'phoneNumber';
    const OPTION_DOUBLE_FIELD_SECOND_NAME = 'secondName';
    const OPTION_NAME = 'name';

    const TYPE_TEXT = 'text';
    const TYPE_HIDDEN = 'hidden';
    const TYPE_PASSWORD = 'password';
    const TYPE_FILE = 'file';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_RADIO = 'radio';
    const TYPE_RADIO_LIST = 'radioList';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_DROP_DOWN_LIST = 'dropDownList';
    const TYPE_SUBMIT = 'submit';
    const TYPE_DOUBLE_PHONE = 'doublePhone';
    const TYPE_RE_CAPTCHA= 'reCaptcha';
    const TYPE_INVISIBLE_CAPTCHA= 'invisibleCaptcha';
    const TYPE_RADIO_COMMENT_LIST= 'radioCommentList';

    public $attribute;
    public $template = "{label}\n{input}\n{error}\n{description}\n{tooltip}";
    public $options = ['class' => ['form-row']]; // for div
    public $inputOptions = ['class' => ['form-input']]; //for input
    public $errorOptions = ['class' => 'error error-text-js']; //for small
    public $labelOptions = ['class' => 'control-label'];
    public $boxOptions = ['class' => 'checkbox-wrap'];
    public $tooltipOptions = ['class' => 'tooltip-target', 'style'=>'display:none;'];
    public $descriptionOptions = ['class' => 'form-field-description'];

    /** @var BasicForm $form */
    protected $_form;
    /** @var Model $form */
    protected $_model;
    protected $_parts = [];
    protected $_inputId;
    protected $_type;
    protected $_canCreateLabel = true;
    protected $_events = [];
    protected $_additionalData = [];
    protected $_additionalClientOptions = [];
    protected $_inputItems = [];
    protected $_defaultErrorTag = 'small';

    /**
     * @param Model $model
     * @param BasicForm $form
     * @param string $attribute
     * @param array $options
     */
    public function __construct(ModelWithFieldInterface $model, BasicForm $form, $attribute, array $options = [])
    {
        $this->_model = $model;
        $this->_form = $form;
        $this->attribute = $attribute;
        $this->_setOptions($options);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Renders the field.
     * @return string
     */
    public function render($content = null)
    {
        if ($content === null) {
            if (!isset($this->_parts['{input}'])) {
                $this->textInput();
            }
            if (!isset($this->_parts['{label}'])) {
                $this->label(false);
            }
            if (!isset($this->_parts['{tooltip}'])) {
                $this->tooltip(false);
            }
            if (!isset($this->_parts['{description}'])) {
                $this->description(false);
            }
            if (!isset($this->_parts['{error}'])) {
                $this->error();
            }
            $content = strtr($this->template, $this->_parts);
        } elseif (!is_string($content)) {
            $content = call_user_func($content, $this);
        }

        return $this->begin() . "\n" . $content . "\n" . $this->end();
    }

    /**
     * Rendering field.
     * @return string the rendering result.
     */
    public function begin()
    {
        $clientOptions = $this->_getClientOptions();
        if (!empty($clientOptions)) {
            $this->_form->addClientOption($clientOptions);
        }

        $inputID = $this->_getInputId();
        $this->options['class'][] = "field-$inputID";
        $this->options['class'][] = "field-{$this->attribute}";
        if ($this->_model->hasErrors($this->attribute)) {
            $this->options['class'][] = $this->_form->errorCssClass;
        }
        $tag = Arr::remove($options, 'tag', 'div');

        return Html::beginTag($tag, $this->_prepareOptions());
    }

    /**
     * Renders the closing tag of the field container.
     * @return string the rendering result.
     */
    public function end()
    {
        return Html::endTag(Arr::keyExists('tag', $this->options) ? $this->options['tag'] : 'div');
    }

    /**
     * @param string $type
     * @param array $options
     * @return static
     * @throws \Exception
     */
    public function input($type, $options = [])
    {
        $this->_setType($type);
        $this->_setInputOptions($options);
        $this->_addLabelFor();
        $this->_parts['{input}'] = Html::activeInput($type, $this->_model, $this->attribute, $this->inputOptions);

        return $this;
    }

    /**
     * @param array $options
     * @return static
     * @throws \Exception
     */
    public function textInput($options = [])
    {
        $this->_setType(static::TYPE_TEXT);
        $this->_setInputOptions($options);
        $this->_addLabelFor();
        $this->_parts['{input}'] = Html::activeInput('text', $this->_model, $this->attribute, $this->inputOptions);

        return $this;
    }

    /**
     * @param array $options
     *
     * @return static
     * @throws \Exception
     */
    public function doublePhoneInput(array $options = [])
    {
        $this->_setType(static::TYPE_DOUBLE_PHONE);

        $codeOptions = Arr::get($options, static::OPTION_PHONE_CODE);
        $numberOptions = Arr::get($options, static::OPTION_PHONE_NUMBER);

        $nameCode = $this->_getInputName($options, static::OPTION_PHONE_CODE);
        $nameNumber = $this->_getInputName($options, static::OPTION_PHONE_NUMBER);

        $valueCode = $this->_getInputValue($options, static::OPTION_PHONE_CODE);
        $valueNumber = $this->_getInputValue($options, static::OPTION_PHONE_NUMBER);

        $codeOptions = $this->_prepareInputOptions($codeOptions);
        $numberOptions = $this->_prepareInputOptions($numberOptions);
        $this->inputOptions = array_merge(
            $options,
            [
                static::OPTION_PHONE_CODE => $codeOptions,
                static::OPTION_PHONE_NUMBER => $numberOptions,
            ]
        );

        $input = '';
        if ($label = Arr::get($codeOptions, Html::OPTION_LABEL)) {
            $input .= Html::label($label, $codeOptions[Html::OPTION_ID]);
            unset($codeOptions[Html::OPTION_LABEL], $codeOptions[static::OPTION_DOUBLE_FIELD_SECOND_NAME]);
        }
        $input .= Html::input('text', $nameCode, $valueCode, $codeOptions);

        if ($label = Arr::get($numberOptions, Html::OPTION_LABEL)) {
            $input .= Html::label($label, $numberOptions[Html::OPTION_ID]);
            unset($numberOptions[Html::OPTION_LABEL], $numberOptions[static::OPTION_DOUBLE_FIELD_SECOND_NAME]);
        }
        $input .= Html::input('text', $nameNumber, $valueNumber, $numberOptions);

        $this->_parts['{input}'] = $input;

        return $this;
    }

    /**
     * @param array $options
     * @return static
     * @throws \Exception
     */
    public function hiddenInput($options = [])
    {
        $this->_setType(static::TYPE_HIDDEN);
        $this->_setInputOptions($options, false);
        $this->_parts['{label}'] = '';
        $this->_parts['{input}'] = Html::activeInput('hidden', $this->_model, $this->attribute, $this->inputOptions);

        return $this;
    }

    /**
     * @param array $options
     * @return static
     * @throws \Exception
     */
    public function passwordInput($options = [])
    {
        $this->_setType(static::TYPE_PASSWORD);
        $this->_setInputOptions($options);
        $this->_addLabelFor();
        $this->_parts['{input}'] = Html::activeInput('password', $this->_model, $this->attribute, $this->inputOptions);

        return $this;
    }

    /**
     * @param array $options
     * @return static
     * @throws \Exception
     */
    public function fileInput($options = [])
    {
        if (!isset($this->_form->options['enctype'])) {
            $this->_form->options['enctype'] = 'multipart/form-data';
        }
        $this->_setType(static::TYPE_FILE);
        $this->_setInputOptions($options);
        $this->_addLabelFor();
        $this->_parts['{input}'] = Html::activeInput('file', $this->_model, $this->attribute, $this->inputOptions);

        return $this;
    }

    /**
     * @param array $options
     * @return static
     * @throws \Exception
     */
    public function textarea($options = [])
    {
        $this->_setType(static::TYPE_TEXTAREA);
        $this->_setInputOptions($options);
        $this->_addLabelFor();
        $this->_parts['{input}'] = Html::activeTextarea($this->_model, $this->attribute, $this->inputOptions);

        return $this;
    }

    /**
     * @param array $options
     * @return static
     * @throws \Exception
     */
    public function radio($options = [])
    {
        $this->_setType(static::TYPE_RADIO);
        $this->_setInputOptions($options);
        $this->_parts['{label}'] = '';
        $this->_parts['{input}'] = Html::activeBooleanInput('radio', $this->_model, $this->attribute, $this->inputOptions);

        return $this;
    }

    /**
     * @param array $items
     * @param array $options
     * @return static
     * @throws \Exception
     */
    public function radioList($items, $options = [])
    {
        $this->_setType(static::TYPE_RADIO_LIST);
        $this->_setInputOptions($options);
        $this->_addLabelFor();
        $this->_inputItems = $items;
        $this->_parts['{input}'] = Html::activeListInput(static::TYPE_RADIO_LIST, $this->_model, $this->attribute, $items, $this->inputOptions);

        return $this;
    }

    /**
     * @param array $items
     * @param array $options
     * @return static
     * @throws \Exception
     */
    public function radioCommentList($items, $options = [])
    {
        $this->template = "{label}\n{input}\n{tooltip}\n{description}";
        $this->_setType(static::TYPE_RADIO_COMMENT_LIST);
        $this->_setInputOptions($options);
        $this->_addLabelFor();
        $this->_inputItems = $items;
        $options = $this->inputOptions;
        if ($this->_model->hasErrors($this->attribute)) {
            $options['error'] = $this->_model->getFirstError($this->attribute);
            $options['errorOptions'] = $this->_getErrorOptions();
        }
        $this->_parts['{input}'] = Html::activeListInput(
            static::TYPE_RADIO_COMMENT_LIST,
            $this->_model,
            $this->attribute,
            $items,
            $options
        );


        return $this;
    }

    /**
     * @param array $options
     * @return static
     * @throws \Exception
     */
    public function checkbox($options = [])
    {
        if (!isset($options['boxOptions'])) {
            $options['boxOptions'] = $this->boxOptions;
        }
        $this->_setType(static::TYPE_CHECKBOX);
        $this->_setInputOptions($options ,false);
        $this->_parts['{label}'] = '';
        $this->_parts['{input}'] = Html::activeBooleanInput('checkbox', $this->_model, $this->attribute, $this->inputOptions);

        return $this;
    }

    /**
     * @param array $items
     * @param array $options
     * @return static
     * @throws \Exception
     */
    public function dropDownList($items, $options = [])
    {
        $this->_setType(static::TYPE_DROP_DOWN_LIST);
        $this->_setInputOptions($options, false);
        $this->_addLabelFor();
        $this->_inputItems = $items;
        $this->_parts['{input}'] = Html::activeDropDownList($this->_model, $this->attribute, $items, $this->inputOptions);

        return $this;
    }

    /**
     * @param array $options
     *
     * @return static
     * @throws \Exception
     */
    public function submitInput($options = [])
    {
        $this->_canCreateLabel = false;
        $this->_setType(static::TYPE_SUBMIT);
        $this->_setInputOptions($options);
        $this->_parts['{label}'] = '';
        $this->_parts['{input}'] = Html::submitInput(
            $this->_model->getAttributeLabel($this->attribute),
            $this->inputOptions
        );

        return $this;
    }

    /**
     * @param string $siteKey Captcha Site Key
     * @param array $options
     * @return static
     * @throws \Exception
     */
    public function invisibleCaptcha($siteKey, $options = [])
    {
        $this->_setType(static::TYPE_INVISIBLE_CAPTCHA);
        $this->_setInputOptions($options);
        $captchaContainerId = "{$this->_getInputId()}-container";

        $this->_additionalClientOptions = [
            'captchaContainerId' => $captchaContainerId,
            'captchaSiteKey' => $siteKey,
        ];

        $this->_parts['{label}'] = '';
        $this->_parts['{input}'] = Html::activeInput('hidden', $this->_model, $this->attribute, $this->inputOptions)
            . HTML::tag('div', '', [
                'id' => $captchaContainerId
            ]);

        return $this;
    }

    public function tooltip($text, array $options = [])
    {
        if ($text === false) {
            $this->_parts['{tooltip}'] = '';
            return $this;
        }
        if (!empty($options)) {
            $this->tooltipOptions = array_merge($this->tooltipOptions, $options);
        }

        $this->options['class'][] = 'tooltip-toggle';
        $this->_parts['{tooltip}'] = Html::tag('div', $text, $this->tooltipOptions);

        return $this;
    }

    /**
     * @param       $text
     * @param array $options
     *
     * @return $this
     */
    public function description($text, array $options = [])
    {
        if ($text === false) {
            $this->_parts['{description}'] = '';
            return $this;
        }
        if (!empty($options)) {
            $this->descriptionOptions = array_merge($this->descriptionOptions, $options);
        }

        $this->_parts['{description}'] = Html::tag('div', $text, $this->descriptionOptions);

        return $this;
    }

    /**
     * @param null|string|false $label
     * @param null|array $options
     * @return $this.
     */
    public function label($label = null, $options = [])
    {
        if (!$this->_canCreateLabel || $label === false) {
            $this->_parts['{label}'] = '';
            return $this;
        }

        $options = array_merge($this->labelOptions, $options);
        if ($label !== null) {
            $options['label'] = $label;
        }

        $for = Arr::remove($options, 'for', $this->_getInputId());
        $label = Arr::remove($options, 'label', $this->_model->getAttributeLabel($this->attribute));
        $this->_parts['{label}'] = Html::label($label, $for, $options);

        return $this;
    }

    /**
     * @param array|false $options
     * @return $this
     */
    public function error($options = [])
    {
        if ($options === false) {
            $this->_parts['{error}'] = '';
            return $this;
        }
        $this->_parts['{error}'] = Html::error($this->_model, $this->attribute, $this->_getErrorOptions($options));

        return $this;
    }

    /**
     * @param string $html
     * @return $this
     */
    public function htmlBlock($html)
    {
        if (empty($html)) {
            $this->_parts['{htmlBlock}'] = '';
        } else {
            $this->_parts['{htmlBlock}'] = $html;
        }

        return $this;
    }

    /**
     * Render captcha as form field
     *
     * @param string $captchaType
     * @param array $options
     * @return $this
     *
     * @author Alexander Shpak <alexander.shpak@tstechpro.com>
     */
    public function captcha($captchaType = 'recaptcha', array $options = [])
    {
        $this->_setType(static::TYPE_RE_CAPTCHA);
        /** @var ReCaptcha|Invisible_ReCaptcha $captcha */
        $captcha = Captcha_Abstract::factory($captchaType);
        $this->_parts['{input}'] = $captcha->render($options);
        return $this;
    }

    /**
     * @param array $events
     * @return $this
     */
    public function addEvents(array $events)
    {
        $this->_events = array_merge($this->_events, $events);
        return $this;
    }

    /**
     * @param array $data - Additional attribute data
     * @return $this
     */
    public function addAdditionalData(array $data)
    {
        $this->_additionalData = array_merge($this->_additionalData, $data);
        return $this;
    }

    protected function _addLabelFor()
    {
        if (!isset($this->labelOptions['for'])) {
            $this->labelOptions['for'] = $this->_getInputId();
        }
    }

    /**
     * @param      $options
     * @param bool $placeholder
     *
     * @throws \Exception
     */
    protected function _setInputOptions($options, $placeholder = true)
    {
        $this->inputOptions = $this->_prepareInputOptions($options, $placeholder);
        $this->_inputId = $this->inputOptions['id'];

    }

    /**
     * @param      $options
     * @param bool $placeholder
     *
     * @return mixed
     * @throws \Exception
     */
    protected function _prepareInputOptions($options, $placeholder = true)
    {
        if ($placeholder && !isset($options['placeholder'])) {
            $options['placeholder'] = $this->_model->getAttributeLabel($this->attribute);
        }
        $this->_prepareInputId($options);
        $this->_prepareInputClasses($options);

        return $options;
    }

    /**
     * @param $options
     *
     * @throws \Exception
     */
    protected function _prepareInputId(&$options)
    {
        if (!empty($options['id'])) {
            return;
        }

        $inputId = $this->_getInputId();
        if ($secondName = Arr::get($options, static::OPTION_DOUBLE_FIELD_SECOND_NAME)) {
            $inputId .= "-$secondName";
        }

        $options['id'] = $inputId;
    }

    /**
     * @param $options
     */
    protected function _prepareInputClasses(&$options)
    {
        $classes = explode(' ', Arr::get($options, 'class', ''));
        $classes = array_merge($classes, Arr::get($this->inputOptions, 'class',  []));
        $classes[] = Html::getInputClass($this->attribute);
        if ($secondName = Arr::get($options, static::OPTION_DOUBLE_FIELD_SECOND_NAME)) {
            $classes[] = Html::getInputClass($this->attribute) . "-$secondName";
        }

        $options['class'] =  implode(' ', $classes);
    }

    /**
     * Returns the JS options for the field.
     * @return array the JS options.
     * @throws \Exception
     */
    protected function _getClientOptions()
    {
        if (!$this->_form->enableClientValidation) {
            return [];
        }

        if (!isset($this->_model->{$this->attribute}) && !property_exists($this->_model, $this->attribute)) {
            return [];
        }

        $isRequired = false;
        $isUniqueGlobal = false;

        $validators = [];
        foreach ($this->_model->getValidators($this->attribute) as $validatorData) {
            $validatorName = $validatorData[0];
            /** @var $validator \tradersoft\model\validator\Validator */
            $validator = 'tradersoft\model\validator\\'.$validatorName;
            if (in_array($validator, [Required::class, Not_Empty::class])) {
                $isRequired = true;
            }
            if ($validator === UniqueGlobal::class) {
                $isUniqueGlobal = true;
            }
            $js = $validator::jsValidate($this->_model, $this->attribute, $validatorData[1]);
            if ($js != '') {
                $conditions = Arr::get($validatorData, Validator::PARAM_CONDITIONS);
                if ($conditions) {
                    $js = new JsExpression('if (checkValidationCondition(' . json_encode(array_values($conditions)) .')) {' . $js .'}');
                }
                $validators[] = $js;
            }
        }

        $options = [];

        $inputID = $this->_getInputId();
        $options['id'] = "#$inputID";
        $options['container'] = ".field-$inputID";
        $options['name'] = $this->attribute;
        $options['realName'] = Arr::get($this->inputOptions, Html::OPTION_NAME, $this->attribute);
        $errorOptions = $this->_getErrorOptions();
        $options['error'] = '.' . implode('.', preg_split('/\s+/', $errorOptions['class'], -1, PREG_SPLIT_NO_EMPTY));
        $options['errorTag'] = $errorOptions['tag'] ?: $this->_defaultErrorTag;
        $options['isRequired'] = $isRequired;
        $options['isUniqueGlobal'] = $isUniqueGlobal;

        if (!empty($validators)) {
            $options['validate'] = new JsExpression('function (attribute, value, messages, checkValidationCondition, field) {' . implode('', $validators) . '}');
        }

        $options['events'] = $this->_events;

        $options['additionalData'] = $this->_additionalData;
        $options['type'] = $this->_type;
        $options['inputOptions'] = $this->inputOptions;
        $options['inputItems'] = $this->_inputItems;

        return $options + $this->_additionalClientOptions;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function _getInputId()
    {
        return $this->_inputId ?: Html::getInputId($this->_model, $this->attribute);
    }

    protected function _setOptions(array $options)
    {
        if (empty($this->options['class'])) {
            $this->options['class'] = [];
        }
        if (isset($options['options']['class'])) {
            $this->options['class'] = array_merge(
                $this->options['class'],
                explode(' ', $options['options']['class'])
            );
        }

        if (isset($options['options']['id'])) {
            $this->options['id'] = $options['options']['id'];
        }

        if (isset($options['template'])) {
            $this->template = $options['template'];
        }
    }

    /**
     * @return array
     */
    protected function _prepareOptions()
    {
        $options = $this->options;
        if (isset($options['options']['class'])) {
            $options['options']['class'] = implode(' ', $options['options']['class']);
        }

        return $options;
    }

    /**
     * @param array  $options
     * @param string $default
     *
     * @return mixed
     */
    protected function _getSecondName(array $options, $default)
    {
        return Arr::get($options, static::OPTION_DOUBLE_FIELD_SECOND_NAME, $default);
    }

    /**
     * @param array       $options
     * @param null|string $doubleFieldOptionName
     *
     * @return mixed|string
     */
    protected function _getInputName(array $options, $doubleFieldOptionName = null)
    {
        $name = Arr::get($options, Html::OPTION_NAME, $this->attribute);
        if (is_null($doubleFieldOptionName)) {
            return $name;
        }

        $doubleFieldOptions = Arr::get($options, $doubleFieldOptionName, []);
        $secondName = $this->_getSecondName($doubleFieldOptions, $doubleFieldOptionName);

        return Arr::get($doubleFieldOptions, Html::OPTION_NAME, $name) . "[$secondName]";
    }

    /**
     * @param array       $options
     * @param null|string $doubleFieldOptionName
     *
     * @return array|mixed|string
     */
    protected function _getInputValue(array $options, $doubleFieldOptionName = null)
    {
        $value = $this->_model->{$this->attribute};
        if ($value instanceof ActiveField) {
            $value = $value->value;
        }

        $options = is_null($doubleFieldOptionName) ? $options : Arr::get($options, $doubleFieldOptionName, []);
        if (is_null($value)) {
            $value = Arr::get($options, Html::OPTION_VALUE);
        }

        if (is_array($value) && !is_null($doubleFieldOptionName)) {
            $value = Arr::get($value, $this->_getSecondName($options, $doubleFieldOptionName));
        }

        return $value;
    }

    /**
     * @param string $viewType
     */
    protected function _setType($viewType)
    {
        $this->_type = $viewType;
        $this->options['class'][] = "field-type-$viewType";
    }

    protected function _getErrorOptions(array $options = [])
    {
        $options['tag'] = $this->_defaultErrorTag;
        return array_merge($this->errorOptions, $options);
    }
}