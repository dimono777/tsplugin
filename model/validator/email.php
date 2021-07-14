<?php
namespace tradersoft\model\validator;

use tradersoft\model\ModelWithFieldInterface;
use tradersoft\helpers\Json;
use tradersoft\helpers\JsExpression;
use tradersoft\helpers\system\Translate;

/**
 * Email validator.
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Email extends AbstractValidator
{
    protected static $_msg = self::VARIABLE_KEY_FIELD_LABEL . ' is not a valid email address';
    private static $expression = '/^[a-zA-Z0-9_\-.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-.]+$/';
    private static $skipOnEmpty = true;


    /**
     * Required validator
     * @param ModelWithFieldInterface $model
     * @param $attribute string
     * @param $value mixed
     * @param $param mixed
     */
    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param)
    {
        if (!static::_checkScalar($model, $attribute, $value)) {
            return;
        }

        if (((isset($param['skipOnEmpty']) && $param['skipOnEmpty']) || static::$skipOnEmpty) && empty($value)) {
            return;
        }

        if (!is_string($value)) {
            $valid = false;
        } elseif (mb_strlen($value) > 254) {
            $valid = false;
        } else {
            $valid = (bool)preg_match(static::$expression, (string)$value);
        }

        if (!$valid) {
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
     * @param ModelWithFieldInterface $model
     * @param $attribute string
     * @param $param mixed
     * @return string
     * @throws \Exception
     */
    public static function jsValidate(ModelWithFieldInterface $model, $attribute, $param)
    {
        $options = [
            'skipOnEmpty' => (int)(isset($param['skipOnEmpty']) ? $param['skipOnEmpty'] : static::$skipOnEmpty),
            'pattern' => new JsExpression(static::$expression),
            'message' => Translate::__(
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute)
            ),
        ];

        return 'validation.email(value, messages, ' . Json::htmlEncode($options) . ');';
    }
}