<?php

namespace tradersoft\model\validator;

use tradersoft\helpers\system\Translate;
use tradersoft\model\ModelWithFieldInterface;

abstract class AbstractValidator implements Validator
{
    const VARIABLE_KEY_FIELD_LABEL = '[FIELD_LABEL]';

    protected static $_msg = 'Invalid field';
    protected static $_msgScalar = self::VARIABLE_KEY_FIELD_LABEL . ' must be scalar';

    /**
     * @param ModelWithFieldInterface  $model
     * @param string $attribute
     * @param string $message
     * @param array  $messageVariables
     */
    protected static function _addError(ModelWithFieldInterface $model, $attribute, $message, array $messageVariables = [])
    {
        $model->addError($attribute, Translate::__($message, $messageVariables));
    }

    /**
     * @param ModelWithFieldInterface  $model
     * @param string $attribute
     *
     * @return array
     */
    protected static function _getMessageVariables(ModelWithFieldInterface $model, $attribute)
    {
        return [
            static::VARIABLE_KEY_FIELD_LABEL => Translate::__($model->getAttributeLabel($attribute)),
        ];
    }

    /**
     * @param $param
     *
     * @return string
     */
    protected static function _getMessage($param)
    {
        if (isset($param[Validator::PARAM_ERROR_MESSAGE_KEY])) {
            return $param[Validator::PARAM_ERROR_MESSAGE_KEY];
        }

        return static::$_msg;
    }

    /**
     * @param ModelWithFieldInterface $model
     * @param $attribute string
     * @param $value     mixed
     *
     * @return bool
     */
    protected static function _checkScalar(ModelWithFieldInterface $model, $attribute, $value)
    {
        if (is_scalar($value) || is_null($value)) {
            return true;
        }

        static::_addError(
            $model,
            $attribute,
            static::$_msgScalar,
            static::_getMessageVariables($model, $attribute)
        );

        return false;
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    protected static function _getExternalValidationName()
    {
        return (new \ReflectionClass(static::class))->getShortName();
    }


    /**
     * @param ModelWithFieldInterface $model
     * @param string                  $attribute
     * @param mixed                   $param
     *
     * @throws \ReflectionException
     */
    protected static function _validateExternal(ModelWithFieldInterface $model, $attribute, $param)
    {
        $externalValidator = static::_getExternalValidationName();
        if ($model->validateInternal($externalValidator, $attribute)) {
            return;
        }

        static::_addError(
            $model,
            $attribute,
            static::_getMessage($param),
            static::_getMessageVariables($model, $attribute)
        );
    }
}