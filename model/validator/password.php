<?php
namespace tradersoft\model\validator;

use tradersoft\model\ModelWithFieldInterface;
use tradersoft\helpers\system\Translate;

/**
 * Password validator.
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Password extends AbstractValidator
{
    protected static $_msg = self::VARIABLE_KEY_FIELD_LABEL . ' must be correct password format';
    public static $validPasswordSymbols = [
        '!', '"', '#', '$', '%', '&', '\'', '(', ')', '*', '+', ',', '-',
        '.', '/', ':', ';', '<', '=', '>', '?', '@', '[', '\\', ']', '^',
        '_', '`', '{', '|', '}', '~'
    ];

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

        $otherSymbols = str_replace(
            static::$validPasswordSymbols,
            '',
            $value
        );

        $valid = !$otherSymbols || ctype_alnum($otherSymbols);
        if (!$valid) {
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
        $options = [
            'message' => Translate::__(
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute)
            ),
        ];

        return 'validation.password(value, messages, ' . json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ');';
    }
}