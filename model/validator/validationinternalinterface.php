<?php

namespace tradersoft\model\validator;

interface ValidationInternalInterface
{
    /**
     * @param string $validatorName
     * @param        $attributeName
     *
     * @return bool
     */
    public function hasInternalValidator($validatorName, $attributeName);

    /**
     * @param string $validatorName
     * @param string $attributeName
     *
     * @return bool
     */
    public function validateInternal($validatorName, $attributeName);
}