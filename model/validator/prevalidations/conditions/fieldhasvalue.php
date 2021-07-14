<?php

namespace tradersoft\model\validator\prevalidations\conditions;

class FieldHasValue extends AbstractCondition
{
    const NAME = 'fieldHasValue';

    const PARAM_KEY_FIELD = 'field';
    const PARAM_KEY_FIELD_VALUE = 'fieldValue';

    public function check(array $params)
    {
        $fieldName = $params[static::PARAM_KEY_FIELD];
        $fieldValue = $params[static::PARAM_KEY_FIELD_VALUE];

        return $this->_model->getAttributeValue($fieldName) == $fieldValue;
    }
}