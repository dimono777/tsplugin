<?php

namespace tradersoft\model\validator;

use Exception;
use tradersoft\helpers\system\Translate;
use tradersoft\model\ModelWithFieldInterface;

class threeFieldsDate extends AbstractValidator
{
    protected static $_msg = 'Date is non valid';
    private static $requiredFieldMsg = 'Birthday fields cannot be blank';
    private static $skipOnEmpty = true;

    /**
     * @inheritDoc
     * @throws Exception
     */
    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param)
    {
        if (
            empty($yearField = $param['yearField'])
            || empty($monthField = $param['monthField'])
            || empty($dayField = $param['dayField'])
        ) {
            throw new Exception('"yearField", "monthField" and "dayField" are required');
        }

        $year = $model->getAttributeValue($yearField);
        $month = $model->getAttributeValue($monthField);
        $day = $model->getAttributeValue($dayField);

        $skipOnEmpty = isset($param['skipOnEmpty']) ? $param['skipOnEmpty'] : static::$skipOnEmpty;

        if (
            $skipOnEmpty
            && (empty($year)
                || empty($month)
                || empty($day))
        ) {
            return true;
        }

        if (
            !$skipOnEmpty
            && (empty($year)
                || empty($month)
                || empty($day))
        ) {
            static::_addError($model, $yearField, '');
            static::_addError($model, $monthField, '');
            static::_addError($model, $dayField, static::_getRequiredFieldMsg($param));

            return false;
        }

        if (!self::_checkDateFormat(implode('-', [$year, $month, $day]))) {
            static::_addError($model, $yearField, '');
            static::_addError($model, $monthField, '');
            static::_addError($model, $dayField, static::_getMessage($param));

            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public static function jsValidate(ModelWithFieldInterface $model, $attribute, $param)
    {
        if (
            empty($yearField = $param['yearField'])
            || empty($monthField = $param['monthField'])
            || empty($dayField = $param['dayField'])
        ) {
            throw new Exception('"yearField", "monthField" and "dayField" are required');
        }

        $options = [
            'yearField' => $yearField,
            'monthField' => $monthField,
            'dayField' => $dayField,
            'message' => Translate::__(static::_getMessage($param)),
            'requiredFieldMsg' => Translate::__(static::_getRequiredFieldMsg($param)),
            'skipOnEmpty' => (int)(isset($param['skipOnEmpty']) ? $param['skipOnEmpty'] : static::$skipOnEmpty),
        ];

        $options = json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return "validation.threeFieldsDate(messages, value, $options, field);";
    }

    protected static function _getRequiredFieldMsg($param)
    {
        if (isset($param['requiredFieldMsg'])) {
            return $param['requiredFieldMsg'];
        }

        return static::$requiredFieldMsg;
    }
    /**
     * @param string $date
     *
     * @return bool
     */
    protected static function _checkDateFormat($date)
    {
        try {
            if (strlen($date) < 8) {
                return false;
            }

            $parsedDate = date_parse($date);

            return $parsedDate['error_count'] < 1
                && $parsedDate['warning_count'] < 1
                && $parsedDate['year'] >= 1900;
        } catch (Exception $e) {
            return false;
        }
    }
}