<?php

namespace tradersoft\model\validator;

use tradersoft\helpers\Arr;
use tradersoft\helpers\system\Translate;
use tradersoft\model\ModelWithFieldInterface;
use tradersoft\model\validator\tin\Configuration;
use tradersoft\model\validator\tin\TINValidator;
use Exception;

class SsnTin extends AbstractValidator
{
    const PARAM_KEY_COUNTRY_FIELD = 'countryField';

    protected static $_msg = self::VARIABLE_KEY_FIELD_LABEL . ' invalid SSN/TIN';

    /**
     * @param ModelWithFieldInterface $model
     * @param string                  $attribute
     * @param mixed                   $value
     * @param mixed                   $param
     */
    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param)
    {
        if (!static::_checkScalar($model, $attribute, $value)) {
            return;
        }
        $param = (array)$param;
        $isValid = false;
        if ($country = static::_getCountry($model, $param)) {
            try {
                $validator = new TINValidator(Configuration::getInstance($country));
                $isValid = $validator->validate($model->getAttributeValue($attribute));
            } catch (Exception $e) {
                $isValid = false;
            }
        }

        if (!$isValid) {
            static::_addError(
                $model,
                $attribute,
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute)
            );
        }
    }

    /**
     * @param ModelWithFieldInterface $model
     * @param string                  $attribute
     * @param mixed                   $param
     *
     * @return string
     */
    public static function jsValidate(ModelWithFieldInterface $model, $attribute, $param)
    {
        $options = (array)$param;
        $options['message'] = Translate::__(
            static::_getMessage($param),
            static::_getMessageVariables($model, $attribute)
        );

        return 'validation.ssntin(attribute, value, messages, ' . json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ');';
    }

    /**
     * @param ModelWithFieldInterface $model
     * @param array                   $param
     *
     * @return null|string
     */
    protected static function _getCountry(ModelWithFieldInterface $model, array $param)
    {
        if (!($countryField = Arr::get($param, static::PARAM_KEY_COUNTRY_FIELD))) {
            return '';
        }

        return $model->getAttributeValue($countryField);
    }

}