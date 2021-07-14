<?php

namespace tradersoft\model\form\decorators;

use tradersoft\helpers\Arr;

class CRMStructureBlock
{
    const FIELD_ID = 'id';
    const FIELD_BLOCK_TYPE_ID = 'blockTypeId';
    const FIELD_BLOCK_ATTRIBUTES = 'blockAttributes';

    protected $_structure;

    /**
     * CRMStructureBlock constructor.
     *
     * @param array $structure
     */
    public function __construct(array $structure)
    {
        $this->_structure = $structure;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->_getAttribute(static::FIELD_ID);
    }

    /**
     * @return int|null
     */
    public function getBlockTypeId()
    {
        return $this->_getAttribute(static::FIELD_BLOCK_TYPE_ID);
    }

    /**
     * @return array|null
     */
    public function getBlockAttributes()
    {
        return $this->_getBlocAttributes();
    }

    /**
     * @param string $attributeName
     *
     * @return mixed
     */
    public function getBlockAttribute($attributeName)
    {
        return Arr::get($this->_getBlocAttributes(), $attributeName);
    }

    /**
     * @return array
     */
    protected function _getBlocAttributes()
    {
        $data = [];
        foreach ($this->_getAttribute(static::FIELD_BLOCK_ATTRIBUTES) as $attribute) {
            foreach ($attribute as $key => $value) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * @param string $attributeName
     *
     * @return mixed
     */
    protected function _getAttribute($attributeName)
    {
        return Arr::get($this->_structure, $attributeName);
    }
}