<?php
namespace tradersoft\model\validator;

use tradersoft\model\ModelWithFieldInterface;

class Max_Length extends MaxLength
{
    /**
     * @inheritdoc
     */
    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param)
    {
        parent::validate($model, $attribute, $value, $param);
    }

    public static function jsValidate(ModelWithFieldInterface $model, $attribute, $param)
    {
        return parent::jsValidate($model, $attribute, $param);
    }
}