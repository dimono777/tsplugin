<?php
namespace tradersoft\model\validator;

use tradersoft\model\ModelWithFieldInterface;
use tradersoft\helpers\system\Translate;

/**
 * Required validator.
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Required extends AbstractValidator
{
    protected static $_msg = self::VARIABLE_KEY_FIELD_LABEL . ' cannot be blank';


    /**
     * Required validator
     * @param ModelWithFieldInterface $model
     * @param $attribute string
     * @param $value mixed
     * @param $param mixed
     * @throws \ReflectionException
     */
    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param)
    {
        $externalValidator = static::_getExternalValidationName();
        if ($model->hasInternalValidator($externalValidator, $attribute)) {
            static::_validateExternal($model, $attribute, $param);
            return;
        }

        if (!static::_checkScalar($model, $attribute, $value)) {
            return;
        }

        if ($value === '0' || !empty($value)) {
            return;
        }
        static::_addError(
            $model,
            $attribute,
            static::_getMessage($param),
            static::_getMessageVariables($model, $attribute)
        );
    }

    /**
     * Required validator
     * @param ModelWithFieldInterface $model
     * @param $attribute string
     * @param $param mixed
     * @return string
     */
    public static function jsValidate(ModelWithFieldInterface $model, $attribute, $param)
    {
        $options = [
            'message' => Translate::__(
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute)
            ),
        ];

        return 'validation.required(attribute, value, messages, ' . json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ');';
    }
}