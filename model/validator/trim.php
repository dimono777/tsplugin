<?php

namespace tradersoft\model\validator;

use tradersoft\model\ModelWithFieldInterface;

/**
 * Trim validator.
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Trim implements Validator
{
    /**
     * Required validator
     *
     * @param ModelWithFieldInterface $model
     * @param                     $attribute string
     * @param                     $value     mixed
     * @param                     $param     mixed
     */
    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param)
    {
        $val = trim($value);
        $model->setAttributeValue($attribute, $val);
    }

    /**
     * Required validator
     *
     * @param ModelWithFieldInterface $model
     * @param                     $attribute string
     * @param                     $param     mixed
     *
     * @return string
     */
    public static function jsValidate(ModelWithFieldInterface $model, $attribute, $param)
    {
        $options = [];

        return 'validation.trim(attribute, value, messages, ' . json_encode($options) . ');';
    }
}