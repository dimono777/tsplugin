<?php
namespace tradersoft\model\form\fields;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Html;
use tradersoft\model\validator\Validator;
use tradersoft\traits\ActiveModel;
use tradersoft\helpers\system\Translate;

/**
 * Class ActiveField
 * @package tradersoft\model\form
 *
 * @property string $name
 * @property string|array $value
 * @property string|array $defaultValue
 * @property string $label
 * @property string $tooltip
 * @property string $description
 * @property string $view
 * @property array $items
 * @property array $validationRules
 * @property array $htmlAttribute
 * @property array $events
 * @property bool $isRequired
 * @property int $order
 * @property int $group
 * @property int $isEditable - 1|0
 *
 * @property array $fieldAttributes
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
abstract class ActiveField implements FieldInterface
{
    use ActiveModel;

    const ATTRIBUTE_OPTION_GETTER = 'optionGetter';
    const ATTRIBUTE_LABEL = 'label';
    const ATTRIBUTE_TOOLTIP = 'tooltip';
    const ATTRIBUTE_PLACEHOLDER = 'placeholder';
    const ATTRIBUTE_DESCRIPTION = 'description';
    const ATTRIBUTE_INPUT_ID = 'inputId';
    const ATTRIBUTE_WRAPPER_ID = 'wrapperId';
    const ATTRIBUTE_INPUT_CLASSES = 'inputClasses';
    const ATTRIBUTE_WRAPPER_CLASSES = 'wrapperClasses';
    const ATTRIBUTE_INPUT_SPECIFIC_ATTRIBUTES = 'inputSpecificAttributes';
    const ATTRIBUTE_PROMPT = 'prompt';
    const ATTRIBUTE_PHONE_CODE_LABEL = 'phoneCodeLabel';
    const ATTRIBUTE_PHONE_NUMBER_LABEL = 'phoneNumberLabel';
    const ATTRIBUTE_PHONE_CODE_PLACEHOLDER = 'phoneCodePlaceholder';
    const ATTRIBUTE_PHONE_NUMBER_PLACEHOLDER = 'phoneNumberPlaceholder';
    const ATTRIBUTE_PHONE_CODE_INPUT_ID = 'phoneCodeInputId';
    const ATTRIBUTE_PHONE_NUMBER_INPUT_ID = 'phoneNumberInputId';
    const ATTRIBUTE_PHONE_CODE_INPUT_CLASSES = 'phoneCodeInputClasses';
    const ATTRIBUTE_PHONE_NUMBER_INPUT_CLASSES = 'phoneNumberInputClasses';
    const ATTRIBUTE_CAPTCHA_SITE_KEY = 'captchaSiteKey';

    protected $_templateSettings = [
        self::VIEW_CHECKBOX => "{input}\n{label}\n{tooltip}\n{error}",
    ];

    protected $_inputHtmlAttributes = [];
    protected $_wrapperHtmlAttributes = [];
    protected $_additionalData;
    protected $_defaultValue;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getAttributeValue();
    }

    /**
     * @inheritDoc
     */
    public function getAttributeValue()
    {
        $value = $this->value;
        $defaultValue = $this->defaultValue;

        if (!$this->isEditable) {
            return $defaultValue;
        }

        return !is_null($value) ? $value : $defaultValue;
    }

    /**
     * @inheritDoc
     */
    public function getWrapperOptions()
    {
        $options['options'] = $this->_wrapperHtmlAttributes;

        if (!empty($this->_templateSettings[$this->view])) {
            $options['template'] = $this->_templateSettings[$this->view];
        }

        return $options;
    }

    /**
     * @inheritDoc
     */
    public function getInputHtmlAttributes()
    {
        return $this->_inputHtmlAttributes;
    }

    /**
     * @inheritDoc
     */
    public function getAttributeLabel()
    {
        return do_shortcode(Translate::__($this->label));
    }

    /**
     * @inheritDoc
     */
    public function getAttributeTooltip()
    {
        return do_shortcode(Translate::__($this->tooltip));
    }

    /**
     * @inheritDoc
     */
    public function getAttributeDescription()
    {
        return do_shortcode(Translate::__($this->description));
    }

    /**
     * @inheritDoc
     */
    public function getAttributeRules()
    {
        if (empty($this->validationRules)) {
            return null;
        }
        $rules = [];
        foreach ($this->validationRules as $rule) {
            $rules[] = [
                $this->name,
                $this->_prepareValidationRuleName($rule['name']),
                $this->_prepareValidationRuleParams($rule),
                Validator::PARAM_CONDITIONS => Arr::get($rule, Validator::PARAM_CONDITIONS, [])
            ];
        }

        return $rules;
    }

    /**
     * @inheritDoc
     */
    public function hasEvents()
    {
        return !empty($this->events);
    }

    /**
     * @inheritDoc
     */
    public function hasAdditionalData()
    {
        return !empty($this->_additionalData);
    }

    /**
     * @inheritDoc
     */
    public function getAdditionalData()
    {
        return $this->_additionalData;
    }

    /**
     * @param string $ruleName
     * @return string
     */
    protected function _prepareValidationRuleName($ruleName)
    {
        // Remove after validation aligning
        $ruleNamesMap = [
            'email' => 'emailFormat',
            'password' => 'passwordFormat',
        ];

        if (array_key_exists($ruleName, $ruleNamesMap)) {
            $ruleName = $ruleNamesMap[$ruleName];
        }
        // Remove after validation aligning

        return str_replace(' ', '_', ucwords(str_replace('_', ' ', $ruleName)));
    }

    /**
     * @param $ruleData
     *
     * @return array
     */
    protected function _prepareValidationRuleParams($ruleData)
    {
        $params = isset($ruleData['params']) ? $ruleData['params'] : [];
        if (!isset($params[Validator::PARAM_ERROR_MESSAGE_KEY]) && isset($ruleData['message'])) {
            $params[Validator::PARAM_ERROR_MESSAGE_KEY] = $ruleData['message'];
        }

        return $params;
    }

    protected function _afterLoad()
    {
        $this->_initLabel();
        $this->_initTooltip();
        $this->_initDescription();
        $this->_initInputHtmlAttribute();
        $this->_initWrapperHtmlAttribute();
        $this->_initItems();
        $this->_initAdditionalData();
    }

    /**
     * @param $value
     */
    protected function _setDefaultValue($value)
    {
        $this->_defaultValue = $value;
    }

    /**
     * @return mixed
     */
    protected function _getDefaultValue()
    {
        return $this->_defaultValue;
    }

    protected function _initLabel()
    {
        $this->label = Arr::get($this->fieldAttributes, static::ATTRIBUTE_LABEL);
    }

    protected function _initTooltip()
    {
        $this->tooltip = Arr::get($this->fieldAttributes, static::ATTRIBUTE_TOOLTIP);
    }

    protected function _initDescription()
    {
        $this->description = Arr::get($this->fieldAttributes, static::ATTRIBUTE_DESCRIPTION);
    }

    protected function _initInputHtmlAttribute()
    {
        $this->_initDefaultInputHtmlAttribute();

        if ($this->_inputHtmlAttributes[Html::OPTION_PLACEHOLDER] !== '') {
            $this->_inputHtmlAttributes[Html::OPTION_PLACEHOLDER] = Translate::__(
                $this->_inputHtmlAttributes[Html::OPTION_PLACEHOLDER]
            );
        }
    }

    protected function _initDefaultInputHtmlAttribute()
    {
        $this->_inputHtmlAttributes[Html::OPTION_ID] = Arr::get($this->fieldAttributes, static::ATTRIBUTE_INPUT_ID);
        $this->_inputHtmlAttributes[Html::OPTION_CLASS] = Arr::get($this->fieldAttributes, static::ATTRIBUTE_INPUT_CLASSES);
        $this->_inputHtmlAttributes[Html::OPTION_PLACEHOLDER] = Arr::get($this->fieldAttributes, static::ATTRIBUTE_PLACEHOLDER);

        if (!$this->isEditable) {
            $this->_inputHtmlAttributes['disabled'] = 'disabled';
        }

        if(!empty($this->defaultValue)) {
            $this->_inputHtmlAttributes[Html::OPTION_VALUE] = $this->defaultValue;
        }

        if ($specificAttributes = Arr::get($this->fieldAttributes, static::ATTRIBUTE_INPUT_SPECIFIC_ATTRIBUTES)) {
            $this->_inputHtmlAttributes = array_merge($this->_inputHtmlAttributes, $specificAttributes);
        }
    }

    protected function _getFieldOptionsByAttributes(array $comparisonList)
    {
        $result = [];
        foreach ($comparisonList as $attributeName => $optionName) {
            if($attrValue = Arr::get($this->fieldAttributes, $attributeName)) {
                $result[$optionName] = $attrValue;
            }
        }

        return $result;
    }

    protected function _initWrapperHtmlAttribute()
    {
        $this->_wrapperHtmlAttributes['id'] = Arr::get($this->fieldAttributes, 'wrapperId');
        $this->_wrapperHtmlAttributes['class'] = Arr::get($this->fieldAttributes, 'wrapperClasses');
    }

    protected function _initItems()
    {
        $this->items = Arr::path($this->fieldAttributes, 'optionGetter.data', []);
    }

    protected function _initAdditionalData()
    {
        $this->_additionalData = Arr::path($this->fieldAttributes, 'optionGetter.extraData', []);
    }
}