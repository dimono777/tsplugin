<?php
namespace tradersoft\model\validator;

use tradersoft\model\ModelWithFieldInterface;
use tradersoft\helpers\system\Translate;

/**
 * InArray validator.
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class InArray extends AbstractValidator
{
    const VARIABLE_KEY_LIST = '[LIST]';

    protected static $_msg = self::VARIABLE_KEY_FIELD_LABEL . ' must be in list ' . self::VARIABLE_KEY_LIST;
    protected static $_list = [];

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

        if (!in_array($value, $param['array'])) {
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

        $options = [
            'list' => json_encode($param['array']),
            'message' => Translate::__(
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute)
            ),
        ];

        return 'validation.inArray(value, messages, ' . json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ');';
    }

    /**
     * @inheritdoc
     */
    protected static function _getMessageVariables(ModelWithFieldInterface $model, $attribute)
    {
        return parent::_getMessageVariables($model, $attribute) + [
            static::VARIABLE_KEY_LIST => implode(', ', static::$_list),
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
        if (!isset($param['array'])) {
            throw new \Exception('The "array" property must be set.');
        }
        if (!is_array($param['array'])) {
            throw new \Exception('The "array" property must be array.');
        }

        static::_initParams($param);
    }

    private static function _initParams($param)
    {
        static::$_list = $param['array'];
    }
}