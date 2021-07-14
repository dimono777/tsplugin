<?php
namespace tradersoft\model\validator;

use tradersoft\model\ModelWithFieldInterface;

class Regex extends Match
{
    /**
     * @inheritdoc
     */
    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param)
    {
        parent::validate($model, $attribute, $value, static::_normalizeParam($param));
    }

    public static function jsValidate(ModelWithFieldInterface $model, $attribute, $param)
    {
        return parent::jsValidate($model, $attribute, static::_normalizeParam($param));
    }

    protected static function _normalizeParam($param)
    {
        if (isset($param[0])) {
            $param['pattern'] = $param[0];
            unset($param[0]);
        }

        return $param;
    }
}