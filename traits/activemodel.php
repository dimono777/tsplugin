<?php
namespace tradersoft\traits;

use tradersoft\helpers\Arr;

/**
 * Trait ActiveModel
 * @package tradersoft\traits
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
trait ActiveModel
{
    use IteratorTrait;

    /**
     * Model data.
     * @var array
     */
    protected $_attributesData = [];

    /**
     * Old model data.
     * @var array
     */
    protected $_attributesOldData = [];

    /**
     * @param array $data
     * @param string  $formName
     */
    public function load(array $data, $formName = null)
    {
        foreach ($data as $attributeName => $attributeValue) {
            $this->{$attributeName} = $attributeValue;
        }
        $this->_afterLoad();
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $publicMethodName = 'get' . ucfirst($name);
        $protectedMethodName = '_' . $publicMethodName;

        if (method_exists($this, $publicMethodName)) {
            return $this->$publicMethodName();
        } elseif (method_exists($this, $protectedMethodName)) {
            return $this->$protectedMethodName();
        }

        return Arr::get($this->_attributesData, $name);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $publicMethodName = 'set' . ucfirst($name);
        $protectedMethodName = '_' . $publicMethodName;

        if (method_exists($this, $publicMethodName)) {
            $this->$publicMethodName($value);
            return;
        } elseif (method_exists($this, $protectedMethodName)) {
            $this->$protectedMethodName($value);
            return;
        }

        if (isset($this->_attributesData[$name])) {
            $this->_attributesOldData[$name] = $this->_attributesData[$name];
        }
        $this->_attributesData[$name] = $value;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->_attributesData);
    }

    /**
     * @param string $name
     */
    public function __unset($name)
    {
        unset($this->_attributesData[$name]);
    }

    /**
     * @return bool
     */
    public function isChanged()
    {
        return !empty($this->_attributesOldData);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isAttributeChanged($name)
    {
        return array_key_exists($name, $this->_attributesOldData);
    }

    /**
     * @return array
     */
    public function getChangedAttributes()
    {
        return array_diff_assoc($this->_attributesData, $this->_attributesOldData);
    }

    public function attributes()
    {
        return array_keys($this->_attributesData);
    }

    public function attributesData()
    {
        return $this->_attributesData;
    }

    protected function _afterLoad()
    {}
}