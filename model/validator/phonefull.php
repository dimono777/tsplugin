<?php

namespace tradersoft\model\validator;

use tradersoft\helpers\ExternalFormValidationRule;
use tradersoft\model\DynamicValidationModel;
use tradersoft\model\ModelWithFieldInterface;

/**
 * Class PhoneFull
 */
class PhoneFull extends AbstractValidator
{
    private static $_skipOnEmpty = true;

    private static $_fields = [];

    /**
     * @inheritDoc
     */
    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param)
    {
        static::_init($param);

        $totalString = implode('', array_merge(static::$_fields, [$value]));
        if (static::$_skipOnEmpty && empty($totalString)) {
            return;
        }

        $rules = ExternalFormValidationRule::getFieldRules(
            $attribute,
            ExternalFormValidationRule::FIELD_PHONE
        );

        if (!$rules) {
            return;
        }

        $dynamicValidationModel = new DynamicValidationModel();
        $dynamicValidationModel->setAttributesLabel([$attribute => $model->getAttributeLabel($attribute)]);
        $dynamicValidationModel->load([$attribute => $totalString]);
        $dynamicValidationModel->setRules($rules);

        if (!$dynamicValidationModel->validate()) {
            $model->addError($attribute, $dynamicValidationModel->getFirstError($attribute));
            foreach (static::$_fields as $fieldAttribute => $value) {
                $model->addError($fieldAttribute);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public static function jsValidate(ModelWithFieldInterface $model, $attribute, $param)
    {
        return '';
    }

    /**
     * @param $param
     */
    protected static function _init($param)
    {
        if (array_key_exists('skipOnEmpty', $param)) {
            static::$_skipOnEmpty = (bool) $param['skipOnEmpty'];
        }

        if (!empty($param['fields']) && is_array($param['fields'])) {
            static::$_fields = $param['fields'];
        }
    }
}