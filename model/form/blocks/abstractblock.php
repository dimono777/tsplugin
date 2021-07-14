<?php

namespace tradersoft\model\form\blocks;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Html;
use tradersoft\model\form\decorators\CRMStructureBlock;
use tradersoft\model\form\fields\FieldInterface;
use tradersoft\model\ModelWithBlockInterface;
use tradersoft\model\validator\ValidationTrait;
use tradersoft\traits\ActiveModel;
use tradersoft\model\form\fields\Factory;

/**
 * Class AbstractBlock
 *
 * @package tradersoft\model\form\blocks
 */
abstract class AbstractBlock implements BlockInterface, \Iterator
{
    use ActiveModel{
        load as loadStructure;
    }
    use ValidationTrait;

    /**
     * @var CRMStructureBlock
     */
    protected $_blockStructure;
    protected $_index;

    /**
     * @var ModelWithBlockInterface
     */
    protected $_parentModel;

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function __construct(array $fieldsData = [], CRMStructureBlock $blockStructure = null)
    {
        $this->_blockStructure = $blockStructure;
        $this->_initFields($fieldsData);
    }

    public function __clone()
    {
        if (!is_null($this->_blockStructure)) {
            $this->_blockStructure = clone $this->_blockStructure;
        }
        foreach ($this->attributesData() as $attrName => $attr) {
            if (is_object($attr)) {
                $this->$attrName = clone $attr;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getBlockId()
    {
        if (is_null($this->_blockStructure)) {
            return 0;
        }

        return (int)$this->_blockStructure->getId();
    }

    /**
     * @param int $index
     */
    public function setIndex($index)
    {
        $this->_index = $index;
    }

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->_index;
    }

    /**
     * @inheritDoc
     */
    public function getBlockAttribute($blockAttributeName)
    {
        if (is_null($this->_blockStructure)) {
            return null;
        }
        return $this->_blockStructure->getBlockAttribute($blockAttributeName);
    }

    /**
     * @inheritDoc
     */
    public function getViewOptions()
    {
        return [
            Html::OPTION_CLASS => $this->getBlockAttribute(static::BLOCK_ATTR_CLASS),
        ];
    }

    /**
     * @inheritDoc
     */
    public function isRepeatable()
    {
        return (bool)$this->getBlockAttribute(static::BLOCK_ATTR_REPEAT_CNT);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getBlockId();
    }

    /**
     * @inheritDoc
     */
    public function getRelationModels()
    {
        $index = null;
        if ($this->isRepeatable()) {
            $models = $this->_parentModel->getBlocksByName($this->getName());
            $index = $this->getIndex();
        } else {
            $models = $this->_parentModel->getBlocksByTypeId($this->getBlockTypeId());
            foreach ($models as $key => $model) {
                if ($this->getBlockId() == $model->getBlockId() && $this->getIndex() == $model->getIndex()) {
                    $index = $key;
                }
            }
        }

        if (isset($models[$index])) {
            unset($models[$index]);
        }

        return $models;
    }

    /**
     * @inheritDoc
     */
    public function setParentModel(ModelWithBlockInterface $model)
    {
        $this->_parentModel = $model;
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        $rules = [];
        foreach ($this->getFields() as $field) {

            if ($fieldRule = $field->getAttributeRules()) {
                $rules = array_merge($rules, $fieldRule);
            }
        }

        return $rules;
    }

    /**
     * @inheritDoc
     */
    public function attributes()
    {
        return array_keys($this->getFields());
    }

    /**
     * @inheritDoc
     */
    public function formName()
    {
        return $this->getName();
    }

    /**
     * @inheritDoc
     */
    public function getAttributeLabel($attribute)
    {
        if (!($field = $this->_getFieldByName($attribute))) {
            return null;
        }

        return $field->getAttributeLabel();
    }

    /**
     * @inheritDoc
     */
    public function hasAttribute($attribute)
    {
        return !is_null($this->_getFieldByName($attribute));
    }

    /**
     * @param $attribute
     *
     * @return array|string|null
     */
    public function getAttributeValue($attribute)
    {
        if (!($field = $this->_getFieldByName($attribute))) {
            return null;
        }

        return $field->getAttributeValue();
    }

    /**
     * @inheritDoc
     */
    public function setAttributeValue($attributeName, $value)
    {
        if ($field = $this->_getFieldByName($attributeName)) {
            $field->value = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function load(array $data = [], $formName = null)
    {
        $formName = $formName ? : $this->formName();
        $formData = Arr::get($data, $formName);

        if (!empty($formData) && is_array($formData)) {
            $this->setAttributes($formData);
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function setAttributes(array $values)
    {
        foreach ($this->attributes() as $attrName) {
            if (isset($values[$attrName])) {
                $this->_getFieldByName($attrName)->value = $values[$attrName];
            }
        }
    }

    /**
     * @return FieldInterface[]
     */
    public function getFields()
    {
        $data = [];
        foreach ($this as $field) {
            if (!($field instanceof FieldInterface)) {
                continue;
            }
            $data[$field->name] = $field;
        }

        return $data;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function addField(FieldInterface $field)
    {
        $name = $field->name;
        if (isset($this->{$name})) {
            throw new \Exception("Field already exists. [field=$name]");
        }
        $this->{$name} = $field;
    }

    /**
     * @inheritDoc
     */
    public function getAttributesValues()
    {
        $data = [];
        foreach ($this->getFields() as $field) {
            if (!$field->isEditable) {
                continue;
            }
            $data[$field->name] = $field->getAttributeValue();
        }

        return $data;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function hasInternalValidator($validatorName, $attributeName)
    {
        if (!($field = $this->_getFieldByName($attributeName))) {
            return false;
        }

        $validatorName = $this->_getInternalValidatorName($validatorName);

        return method_exists($field, $validatorName);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function validateInternal($validatorName, $attributeName)
    {
        if (!($field = $this->_getFieldByName($attributeName))) {
            throw new \Exception("Unknown attribute. [attributeName=$attributeName]");
        }

        $validatorName = $this->_getInternalValidatorName($validatorName);
        if (!method_exists($field, $validatorName)) {
            throw new \Exception("Unknown validator. [validatorName=$validatorName]");
        }

        return (bool)$field->$validatorName();
    }

    /**
     * @param $validatorName
     *
     * @return string
     */
    protected function _getInternalValidatorName($validatorName)
    {
        return 'validate' . ucfirst($validatorName);
    }

    /**
     * Initialization block fields
     * @param array $fieldsData
     * @throws \Exception
     */
    protected function _initFields(array $fieldsData)
    {
        $fields = Arr::index($fieldsData, 'name');
        $formFields = [];
        foreach ($fields as $key => $fieldData) {
            $field = Factory::createField($fieldData['view']);
            $field->load($fieldData);
            $formFields[$key] = $field;
        }

        $this->loadStructure($formFields);
    }

    /**
     * @param string $fieldName
     *
     * @return FieldInterface|null
     */
    protected function _getFieldByName($fieldName)
    {
        return Arr::get($this->getFields(), $fieldName);
    }

}