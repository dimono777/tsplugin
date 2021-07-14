<?php

namespace tradersoft\model\validator;

use tradersoft\helpers\system\Translate;
use tradersoft\model\ModelWithFieldInterface;

class UniqueGlobal extends AbstractValidator
{
    protected static $_msg = self::VARIABLE_KEY_FIELD_LABEL . ' must be unique';

    /**
     * @param ModelWithFieldInterface $model
     * @param string                  $attribute
     * @param mixed                   $value
     * @param mixed                   $param
     */
    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param)
    {
        if (!static::_checkScalar($model, $attribute, $value)) {
            return;
        }

        $relationModels = $model->getRelationModels();
        $currentValue = $model->getAttributeValue($attribute);

        $isValid = true;
        foreach ($relationModels as $rModel){
            if ($currentValue == $rModel->getAttributeValue($attribute)) {
                $isValid = false;
            }
        }

        if (!$isValid) {
            static::_addError(
                $model,
                $attribute,
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute)
            );
        }
    }

    /**
     * @param ModelWithFieldInterface $model
     * @param string                  $attribute
     * @param mixed                   $param
     *
     * @return string
     */
    public static function jsValidate(ModelWithFieldInterface $model, $attribute, $param)
    {
        $options = [
            'message' => Translate::__(
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute)
            ),
            'attribute' => $attribute,
            'formName' => $model->formName(),
        ];

        $jsonOptions = json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return "validation.uniqueGlobal(value, messages, $jsonOptions);";
    }

}