<?php
namespace tradersoft\model\validator;

use tradersoft\model\ModelWithFieldInterface;
use tradersoft\helpers\system\Translate;

/**
 * Boolean validator.
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Boolean extends AbstractValidator
{
    protected static $_msg = self::VARIABLE_KEY_FIELD_LABEL . ' must be set';
    protected static $_strict = false;

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
        static::_checkParam($param);
        static::_prepareParam($param);

        $valid = (!$param['strict'] && $value == $param['value']) || ($param['strict'] && $value === $param['value']);

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
        static::_checkParam($param);
        static::_prepareParam($param);

        $options = [
            'value' => $param['value'],
            'strict' => $param['strict'],
            'message' => Translate::__(
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute)
            ),
        ];

        return 'validation.boolean(value, messages, ' . json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ');';
    }

    /**
     * @param $param mixed
     * @throws \Exception
     */
    private static function _checkParam($param)
    {
        if (empty($param)) {
            throw new \Exception('Boolean validator property must be set.');
        }
        if (!is_array($param)) {
            throw new \Exception('Boolean validator property must be array.');
        }
        if (!isset($param['value'])) {
            throw new \Exception('The "value" property must be set.');
        }
    }

    /**
     * @param $param mixed
     */
    private static function _prepareParam(&$param)
    {
        if (isset($param['strict'])) {
            $param['strict'] = (bool)$param['strict'];
        } else {
            $param['strict'] = static::$_strict;
        }
    }
}