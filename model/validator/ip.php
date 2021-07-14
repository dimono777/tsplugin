<?php
namespace tradersoft\model\validator;

use tradersoft\helpers\Arr;
use tradersoft\model\ModelWithFieldInterface;
use tradersoft\helpers\system\Translate;

/**
 * Ip validator.
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Ip extends AbstractValidator
{
    protected static $_msg = self::VARIABLE_KEY_FIELD_LABEL . ' invalid ip address';
    protected static $_skipOnEmpty = true;

    /**
     * Required validator
     * @param ModelWithFieldInterface $model
     * @param $attribute string
     * @param $value mixed
     * @param $param mixed
     * @throws \Exception
     */
    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param)
    {
        if (!static::_checkScalar($model, $attribute, $value)) {
            return;
        }

        static::_prepareParam($param);

        if ($param['skipOnEmpty'] && empty($value)) {
            return;
        }

        if (!(bool)filter_var($value, FILTER_VALIDATE_IP)) {
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
        static::_prepareParam($param);

        $options = [
            'skipOnEmpty' => (int)$param['skipOnEmpty'],
            'message' => Translate::__(
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute)
            ),
        ];

        return 'validation.ip(value, messages, ' . json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ');';
    }

    /**
     * @param $param mixed
     */
    protected static function _prepareParam(&$param)
    {
        $param['skipOnEmpty'] = Arr::get($param, 'skipOnEmpty', static::$_skipOnEmpty);
    }
}