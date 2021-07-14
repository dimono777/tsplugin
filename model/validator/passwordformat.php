<?php

namespace tradersoft\model\validator;

use tradersoft\helpers\ExternalFormValidationRule;
use tradersoft\model\ModelWithFieldInterface;

/**
 * Password format validator.
 */
class PasswordFormat extends Match
{
    protected static $_msg = self::VARIABLE_KEY_FIELD_LABEL . ' must be correct password format';

    /**
     * Required validator
     *
     * @param ModelWithFieldInterface $model
     * @param string                  $attribute
     * @param mixed                   $value
     * @param mixed                   $param
     *
     * @throws \Exception
     */
    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param)
    {
        static::_init($param);
        if (!isset($param['pattern'])) {
            return;
        }

        parent::validate($model, $attribute, $value, $param);
    }

    /**
     * Required validator
     *
     * @param ModelWithFieldInterface $model
     * @param                         $attribute string
     * @param                         $param     mixed
     *
     * @return string
     * @throws \Exception
     */
    public static function jsValidate(ModelWithFieldInterface $model, $attribute, $param)
    {
        static::_init($param);
        if (!isset($param['pattern'])) {
            return '';
        }

        return parent::jsValidate($model, $attribute, $param);
    }

    /**
     * @param $param
     */
    protected static function _init(&$param)
    {
        $externalParam = ExternalFormValidationRule::getFieldRuleParams(
            ExternalFormValidationRule::FIELD_PASSWORD,
            ExternalFormValidationRule::VALIDATOR_PASSWORD
        );

        $externalParam = is_array($externalParam) ? $externalParam : ['pattern' => $externalParam];

        $param = array_merge($externalParam, (array) $param);
    }
}