<?php

namespace tradersoft\model\form\fields;

class Checkbox extends ActiveField
{
    public function getAttributeValue()
    {
        $value = parent::getAttributeValue();

        return is_null($value) ? null : (int)$value;
    }
}