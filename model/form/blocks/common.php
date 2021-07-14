<?php

namespace tradersoft\model\form\blocks;

class Common extends AbstractBlock
{
    /**
     * @inheritDoc
     */
    public function getBlockTypeId()
    {
        return static::BLOCK_ID_COMMON;
    }
}