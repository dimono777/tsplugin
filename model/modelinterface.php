<?php

namespace tradersoft\model;

interface ModelInterface
{
    /**
     * @return array
     */
    public function attributes();

    /**
     * @param $attribute
     *
     * @return string
     */
    public function getAttributeLabel($attribute);

    /**
     * @param string $attribute
     *
     * @return mixed
     */
    public function getAttributeValue($attribute);

    /**
     * @return array
     */
    public function getAttributesValues();

    /**
     * @param string $attributeName
     * @param mixed  $value
     */
    public function setAttributeValue($attributeName, $value);

    /**
     * @param array $values
     */
    public function setAttributes(array $values);

    /**
     * @param array       $data
     * @param string|null $formName
     *
     * @return bool
     */
    public function load(array $data, $formName = null);
}