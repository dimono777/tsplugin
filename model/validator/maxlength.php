<?php

namespace tradersoft\model\validator;

use tradersoft\model\ModelWithFieldInterface;
use tradersoft\helpers\system\Translate;

/**
 * MaxLength validator.
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class MaxLength extends AbstractValidator
{
    const VARIABLE_KEY_LENGTH = '[MAX_LENGTH]';

    protected static $_msg = self::VARIABLE_KEY_FIELD_LABEL . ' maximum length must be ' . self::VARIABLE_KEY_LENGTH;

    /**
     * Required validator
     *
     * @param ModelWithFieldInterface $model
     * @param $attribute string
     * @param $value     mixed
     * @param $param     mixed
     *
     * @throws \Exception
     */
    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param)
    {
        if (!static::_checkScalar($model, $attribute, $value)) {
            return;
        }

        $max = static::prepareLength($param);
        if (mb_strlen($value, 'UTF-8') > $max) {
            static::_addError(
                $model,
                $attribute,
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute) + [static::VARIABLE_KEY_LENGTH => $max]
            );
        }
    }

    /**
     * Required validator
     *
     * @param ModelWithFieldInterface $model
     * @param $attribute string
     * @param $param     mixed
     *
     * @return string
     * @throws \Exception
     */
    public static function jsValidate(ModelWithFieldInterface $model, $attribute, $param)
    {
        $max = static::prepareLength($param);
        $options = [
            'max' => (int)$max,
            'message' => Translate::__(
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute) + [static::VARIABLE_KEY_LENGTH => $max]
            ),
        ];

        return 'validation.maxLength(value, messages, ' . json_encode(
                $options,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            ) . ');';
    }

    /**
     * @param $param
     *
     * @return int
     * @throws \Exception
     */
    public static function prepareLength($param)
    {
        if (is_array($param)) {
            if (isset($param[0])) {
                return (int)$param[0];
            }
            if (isset($param['length'])) {
                return (int)$param['length'];
            }
            if (isset($param['max'])) {
                return (int)$param['max'];
            }
            throw new \Exception('Invalid params');
        }

        return (int)$param;
    }
}