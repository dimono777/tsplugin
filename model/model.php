<?php
namespace tradersoft\model;

use tradersoft\interfaces\ISystemMessage;
use tradersoft\model\validator\ValidationTrait;
use tradersoft\traits\SystemMessage;

/**
 * Model is the base class for data models.
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Model implements ISystemMessage, ModelWithFieldInterface
{
    use SystemMessage;
    use ValidationTrait;

    public function init()
    {}

    public function save()
    {}

    public function __construct()
    {
        $this->init();
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

    /**
     * Returns the form name that this model class should use.
     * @return string
     * @throws \ReflectionException
     */
    public function formName()
    {
        $reflector = new \ReflectionClass($this);
        return $reflector->getShortName();
    }

    /**
     * @inheritDoc
     */
    public function getRelationModels()
    {
        return [];
    }

    /**
     * Returns the list of attribute names.
     * @return array
     * @throws \ReflectionException
     */
    public function attributes()
    {
        $class = new \ReflectionClass($this);
        $names = [];
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $names[] = $property->getName();
            }
        }

        return $names;
    }

    /**
     * Get array with attribute labels
     */
    public function attributeLabels()
    {
        return [];
    }

    /**
     * Returns the text label for the specified attribute.
     * @param string $attribute
     * @return string
     */
    public function getAttributeLabel($attribute)
    {
        $labels = $this->attributeLabels();
        return isset($labels[$attribute]) ? $labels[$attribute] : \TS_Functions::camel2words($attribute, true);
    }

    /**
     * Get array with validation rules
     */
    public function rules()
    {
        return [];
    }

    /**
     * Load model from data
     * @inheritDoc
     */
    public function load(array $data, $formName = null)
    {
        $this->beforeLoad($data);

        $scope = $formName === null ? $this->formName() : $formName;
        if ($scope && !empty($data)) {
            $this->setAttributes($data);
            $this->afterLoad();
            return true;
        }
        return false;
    }

    public function afterLoad()
    {}

    /**
     * @param array $data
     */
    public function beforeLoad(array $data)
    {}

    /**
     * Sets the attribute values in a massive way.
     * @param array $values attribute values (name => value).
     * @throws \ReflectionException
     */
    public function setAttributes(array $values)
    {
        if (is_array($values)) {
            $attributes = $this->attributes();
            foreach ($values as $name => $value) {
                if (in_array($name, $attributes)) {
                    $this->$name = $value;
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function hasAttribute($attribute)
    {
        return isset($this->{$attribute}) || property_exists($this, $attribute);
    }

    /**
     * @inheritDoc
     */
    public function getAttributeValue($attribute)
    {
        return $this->_getParseAttributeValue($attribute, $this->$attribute);
    }

    /**
     * @inheritDoc
     * @throws \ReflectionException
     */
    public function getAttributesValues()
    {
        $data = [];
        foreach ($this->attributes() as $attrName) {
            $data[$attrName] = $this->$attrName;
        }

        return $data;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function setAttributeValue($attribute, $value)
    {
        if (!$this->hasAttribute($attribute)) {
            throw new \Exception("Unknown attribute. [attribute = $attribute]");
        }
        $this->$attribute = $value;
    }

    protected function _getParseAttributeValue($attrName, $attrValue)
    {
        if (is_null($attrValue)) {
            return null;
        }
        if (in_array($attrName, $this->_getIntAttributes())) {
            return (int)$attrValue;
        }
        if (in_array($attrName, $this->_getBoolAttributes())) {
            return (bool)$attrValue;
        }

        return $attrValue;
    }

    /**
     * @return array
     */
    protected function _getIntAttributes()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function _getBoolAttributes()
    {
        return [];
    }
}