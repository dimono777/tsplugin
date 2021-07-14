<?php
namespace tradersoft\model\validator;

use tradersoft\model\ModelWithFieldInterface;

/**
 * StripTags validator.
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class StripTags implements Validator
{
    /**
     * Required validator
     * @param ModelWithFieldInterface $model
     * @param $attribute string
     * @param $value mixed
     * @param $param mixed
     */
    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param)
    {
        $val = trim(strip_tags($value));
        $model->setAttributeValue($attribute, $val);
    }

    /**
     * Required validator
     * @param ModelWithFieldInterface $model
     * @param $attribute string
     * @param $param mixed
     * @return string
     */
    public static function jsValidate(ModelWithFieldInterface $model, $attribute, $param)
    {
        $options = [];
        return 'validation.stripTags(attribute, value, messages, ' . json_encode($options) . ');';
    }
}