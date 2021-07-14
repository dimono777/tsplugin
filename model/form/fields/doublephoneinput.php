<?php

namespace tradersoft\model\form\fields;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Field;
use tradersoft\helpers\Html;

class DoublePhoneInput extends ActiveField
{
    protected $_phoneCodeSecondName = 'code';
    protected $_phoneNumberSecondName = 'number';

    /**
     * @inheritDoc
     */
    public function getAttributeValue()
    {
        $value = $this->_getDoublePhoneValue();
        $defaultValue = $this->_getDoublePhoneDefaultValue();

        if (!$this->isEditable) {
            return $defaultValue;
        }

        return !is_null($value) ? $value : $defaultValue;
    }

    /**
     * @inheritDoc
     */
    protected function _setDefaultValue($value)
    {
        $this->_defaultValue[$this->_phoneCodeSecondName] = $value;
    }

    /**
     * @inheritDoc
     */
    protected function _initDefaultInputHtmlAttribute()
    {
        $phoneCodeAttributes = $this->_getFieldOptionsByAttributes([
            static::ATTRIBUTE_PHONE_CODE_LABEL => Html::OPTION_LABEL,
            static::ATTRIBUTE_PHONE_CODE_PLACEHOLDER => Html::OPTION_PLACEHOLDER,
            static::ATTRIBUTE_PHONE_CODE_INPUT_ID => Html::OPTION_ID,
            static::ATTRIBUTE_PHONE_CODE_INPUT_CLASSES => Html::OPTION_CLASS,
        ]);
        $phoneCodeAttributes[Field::OPTION_DOUBLE_FIELD_SECOND_NAME] = $this->_phoneCodeSecondName;

        if ($defaultValue = Arr::get($this->defaultValue, $this->_phoneCodeSecondName)) {
            $phoneCodeAttributes[Html::OPTION_VALUE] = $defaultValue;
        }

        $phoneNumberAttributes = $this->_getFieldOptionsByAttributes([
            static::ATTRIBUTE_PHONE_NUMBER_LABEL => Html::OPTION_LABEL,
            static::ATTRIBUTE_PHONE_NUMBER_PLACEHOLDER => Html::OPTION_PLACEHOLDER,
            static::ATTRIBUTE_PHONE_NUMBER_INPUT_ID => Html::OPTION_ID,
            static::ATTRIBUTE_PHONE_NUMBER_INPUT_CLASSES => Html::OPTION_CLASS,
        ]);
        $phoneNumberAttributes[Field::OPTION_DOUBLE_FIELD_SECOND_NAME] = $this->_phoneNumberSecondName;

        if ($defaultValue = Arr::get($this->defaultValue, $this->_phoneNumberSecondName)) {
            $phoneNumberAttributes[Html::OPTION_VALUE] = $defaultValue;
        }

        if (!$this->isEditable) {
            $phoneCodeAttributes[Html::OPTION_DISABLED] = Html::OPTION_DISABLED;
            $phoneNumberAttributes[Html::OPTION_DISABLED] = Html::OPTION_DISABLED;
        }

        $this->_inputHtmlAttributes[Field::OPTION_PHONE_CODE] = $phoneCodeAttributes;
        $this->_inputHtmlAttributes[Field::OPTION_PHONE_NUMBER] = $phoneNumberAttributes;
    }

    protected function _getDoublePhoneValue()
    {
        if (empty($this->value)) {
            return null;
        }

        return $this->value[$this->_phoneCodeSecondName] . $this->value[$this->_phoneNumberSecondName];
    }

    protected function _getDoublePhoneDefaultValue()
    {
        return $this->defaultValue[$this->_phoneCodeSecondName] . $this->defaultValue[$this->_phoneNumberSecondName];
    }
}