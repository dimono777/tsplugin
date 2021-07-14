<?php

namespace tradersoft\model\validator;

use tradersoft\helpers\Arr;
use tradersoft\model\ModelWithFieldInterface;
use tradersoft\helpers\system\Translate;

/**
 * MinLength validator.
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class MinLength extends AbstractValidator
{
    const VARIABLE_KEY_LENGTH = '[MIN_LENGTH]';

    protected static $_msg = self::VARIABLE_KEY_FIELD_LABEL . ' minimum length must be ' . self::VARIABLE_KEY_LENGTH;
    protected static $_skipOnEmpty = false;

    /**
     * Required validator
     *
     * @param ModelWithFieldInterface $model
     * @param $attribute string
     * @param $value     mixed
     * @param $param     mixed
     *
     * @throws \Exception
     */
    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param)
    {
        if (!static::_checkScalar($model, $attribute, $value)) {
            return;
        }

        static::_prepareParam($param);
        $min = static::prepareLength($param);

        if ($param['skipOnEmpty'] && empty($value)) {
            return;
        }

        if (mb_strlen($value, 'UTF-8') < $min) {
            static::_addError(
                $model,
                $attribute,
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute) + [static::VARIABLE_KEY_LENGTH => $min]
            );
        }
    }

    /**
     * Required validator
     *
     * @param ModelWithFieldInterface $model
     * @param $attribute string
     * @param $param     mixed
     *
     * @return string
     * @throws \Exception
     */
    public static function jsValidate(ModelWithFieldInterface $model, $attribute, $param)
    {
        static::_prepareParam($param);
        $min = static::prepareLength($param);

        $options = [
            'skipOnEmpty' => (int)$param['skipOnEmpty'],
            'min' => $min,
            'message' => Translate::__(
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute) + [static::VARIABLE_KEY_LENGTH => $min]
            ),
        ];

        return 'validation.minLength(value, messages, ' . json_encode(
                $options,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            ) . ');';
    }

    /**
     * @param $param
     *
     * @return int
     * @throws \Exception
     */
    public static function prepareLength($param)
    {
        if (is_array($param)) {
            if (isset($param[0])) {
                return (int)$param[0];
            }
            if (isset($param['length'])) {
                return (int)$param['length'];
            }
            if (isset($param['min'])) {
                return (int)$param['min'];
            }
            throw new \Exception('Invalid params');
        }

        return (int)$param;
    }

    /**
     * @param $param mixed
     */
    protected static function _prepareParam(&$param)
    {
        if (!is_array($param)) {
            $param = ['length' => $param];
        }
        $param['skipOnEmpty'] = Arr::get($param, 'skipOnEmpty', static::$_skipOnEmpty);
    }
}