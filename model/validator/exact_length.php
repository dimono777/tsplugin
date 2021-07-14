<?php
namespace tradersoft\model\validator;

use tradersoft\helpers\Arr;
use tradersoft\model\ModelWithFieldInterface;
use tradersoft\helpers\system\Translate;

/**
 * Exact_Length validator.
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Exact_Length extends AbstractValidator
{
    const VARIABLE_KEY_LENGTH = '[LENGTH]';

    protected static $_msg = self::VARIABLE_KEY_FIELD_LABEL . ' length must be ' . self::VARIABLE_KEY_LENGTH;
    protected static $_skipOnEmpty = true;
    protected static $_length;

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

        static::_checkParam($param);
        static::_prepareParam($param);

        if ($param['skipOnEmpty'] && empty($value)) {
            return;
        }

        if (mb_strlen($value) != static::$_length) {
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
            'skipOnEmpty' => (int)$param['skipOnEmpty'],
            'length' => static::$_length,
            'message' => Translate::__(
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute)
            ),
        ];

        return 'validation.exactLength(value, messages, ' . json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ');';
    }

    /**
     * @inheritdoc
     */
    protected static function _getMessageVariables(ModelWithFieldInterface $model, $attribute)
    {
        return parent::_getMessageVariables($model, $attribute) + [
            static::VARIABLE_KEY_LENGTH => static::$_length,
        ];
    }

    /**
     * @param $param mixed
     * @throws \Exception
     */
    private static function _checkParam($param)
    {
        if (empty($param)) {
            throw new \Exception('InArray validator property must be set.');
        }
        if (!is_array($param)) {
            throw new \Exception('InArray validator property must be array.');
        }
        if (!isset($param[0]) && !isset($param['length'])) {
            throw new \Exception('The "length" property must be set.');
        }
    }

    /**
     * @param $param mixed
     */
    private static function _prepareParam(&$param)
    {
        $param['skipOnEmpty'] = Arr::get($param, 'skipOnEmpty', static::$_skipOnEmpty);

        if (!isset($param['length'])) {
            $param['length'] = (int)$param[0];
        }
        static::$_length = $param['length'];
    }
}