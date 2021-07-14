<?php

namespace tradersoft\model\validator;

use tradersoft\helpers\Json;
use tradersoft\helpers\system\Translate;
use tradersoft\model\ModelWithFieldInterface;

/**
 * No_Html validator.
 */
class No_Html extends AbstractValidator
{
    protected static $_msg = self::VARIABLE_KEY_FIELD_LABEL . ' must not contain html tags';

    /**
     * Required validator
     *
     * @param ModelWithFieldInterface $model
     * @param                         $attribute string
     * @param                         $value     mixed
     * @param                         $param     mixed
     *
     * @throws \Exception
     */
    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param)
    {
        if (!static::_checkScalar($model, $attribute, $value)) {
            return;
        }

        if (strip_tags($value) != $value) {
            static::_addError(
                $model,
                $attribute,
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute)
            );
        }
    }

    /**
     * Required validator
     *
     * @param ModelWithFieldInterface $model
     * @param string                  $attribute
     * @param mixed                   $param
     *
     * @return string
     * @throws \Exception
     */
    public static function jsValidate(ModelWithFieldInterface $model, $attribute, $param)
    {
        $options = [
            'message' => Translate::__(
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute)
            ),
        ];

        return 'validation.noHtml(value, messages, ' . Json::htmlEncode($options) . ');';
    }
}