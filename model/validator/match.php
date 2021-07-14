<?php
namespace tradersoft\model\validator;

use tradersoft\helpers\Arr;
use tradersoft\model\ModelWithFieldInterface;
use tradersoft\helpers\Json;
use tradersoft\helpers\JsExpression;
use tradersoft\helpers\system\Translate;

/**
 * Match validator.
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Match extends AbstractValidator
{
    protected static $_msg = self::VARIABLE_KEY_FIELD_LABEL . ' does not match the required format';
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

        if ($param['skipOnEmpty'] && empty($value) && $value !== '0') {
            return;
        }

        $valid = !is_array($value) && preg_match($param['pattern'], $value);

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
        static::_prepareParam($param);

        $options = [
            'skipOnEmpty' => (int)$param['skipOnEmpty'],
            'pattern' => static::preparePatternForJs($param['pattern']),
            'message' => Translate::__(
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute)
            ),
        ];

        return 'validation.match(value, messages, ' . Json::htmlEncode($options) . ');';
    }

    /**
     * @param $pattern
     *
     * @return JsExpression
     */
    public static function preparePatternForJs($pattern)
    {
        if (empty($pattern) || !is_string($pattern)) {
            return null;
        }

        $pattern = trim($pattern);
        $pattern = preg_replace('/\\\\/', '\\\\\\\\', $pattern);
        $pattern = preg_replace('/\'/', '\\\'', $pattern);

        return new JsExpression("'$pattern'");
    }

    /**
     * @param $param mixed
     * @throws \Exception
     */
    protected static function _prepareParam(&$param)
    {
        if (!is_array($param)) {
            $param = ['pattern' => $param];
        }
        if (!isset($param['pattern'])) {
            throw new \Exception('The "pattern" property must be set.');
        }
        $param['skipOnEmpty'] = Arr::get($param, 'skipOnEmpty', static::$_skipOnEmpty);
    }
}