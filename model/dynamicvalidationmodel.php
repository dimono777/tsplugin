<?php

namespace tradersoft\model;

use tradersoft\model\validator\ValidationTrait;
use tradersoft\traits\ActiveModel;

class DynamicValidationModel implements ModelWithFieldInterface
{
    use ActiveModel;
    use ValidationTrait;

    protected $_rules = [];
    protected $_labels = [];

    /**
     * @inheritDoc
     */
    public function hasAttribute($attribute)
    {
        return in_array($attribute, $this->attributes());
    }

    /**
     * @inheritDoc
     */
    public function getAttributeValue($attribute)
    {
        return $this->$attribute;
    }

    /**
     * @inheritDoc
     */
    public function setAttributeValue($attributeName, $value)
    {
        $this->$attributeName = $value;
    }

    /**
     * @param $labels
     */
    public function setAttributesLabel(array $labels)
    {
        $this->_labels = $labels;
    }

    /**
     * @inheritDoc
     */
    public function getAttributeLabel($attribute)
    {
        return isset($this->_labels[$attribute]) ? $this->_labels[$attribute] : $attribute;
    }

    /**
     * @inheritDoc
     */
    public function getAttributesValues()
    {
        return $this->attributesData();
    }

    /**
     * @inheritDoc
     */
    public function setAttributes(array $values)
    {
        foreach ($values as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function formName()
    {
        return static::class;
    }

    /**
     * @inheritDoc
     */
    public function getRelationModels()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function setRules(array $rules)
    {
        $this->_rules = $rules;
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return $this->_rules;
    }

    /**
     * @inheritDoc
     */
    public function hasInternalValidator($validatorName, $attributeName)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function validateInternal($validatorName, $attributeName)
    {
        return true;
    }


}