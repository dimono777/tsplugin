<?php
namespace tradersoft\model\validator;

use tradersoft\model\ModelWithFieldInterface;

/**
 * Validator is the base class for all validators.
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
interface Validator
{
    const PARAM_ERROR_MESSAGE_KEY = 'msg';
    const PARAM_CONDITIONS = 'conditions';

    /**
     * Required validator
     * @param $model ModelWithFieldInterface
     * @param $attribute string
     * @param $value mixed
     * @param $param mixed
     */
    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param);

    /**
     * Required validator
     * @param $model ModelWithFieldInterface
     * @param $attribute string
     * @param $param mixed
     * @return string
     */
    public static function jsValidate(ModelWithFieldInterface $model, $attribute, $param);
}