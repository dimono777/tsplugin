<?php

namespace tradersoft\model\validator;

interface ValidationInterface
{
    /**
     * @return bool
     */
    public function validate();

    /**
     * @param null|string $attributeName
     *
     * @return mixed
     */
    public function getValidators($attributeName = null);

    /**
     * @return array
     */
    public function rules();

    /**
     * @param null|string $attribute
     *
     * @return bool
     */
    public function hasErrors($attribute = null);

    /**
     * @param null|string $attribute
     *
     * @return array
     */
    public function getErrors($attribute = null);

    /**
     * @param $attribute
     *
     * @return null|string
     */
    public function getFirstError($attribute);

    /**
     * @param string $attribute
     * @param string $error
     */
    public function addError($attribute, $error = '');
}