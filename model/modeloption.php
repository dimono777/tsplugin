<?php
namespace tradersoft\model;

trait ModelOption
{

    /**
     * @return array
     */
    public abstract function attributeOptions();

    /**
     * @param $attribute string
     * @param $default array
     * @return array
     */
    public function getAttributeOptions($attribute, $default = [])
    {
        $options = $this->attributeOptions();
        return (isset($options[$attribute])) ? array_merge($options[$attribute], $default) : $default;
    }
}