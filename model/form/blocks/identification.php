<?php

namespace tradersoft\model\form\blocks;

class Identification extends AbstractBlock
{
    /**
     * @inheritDoc
     */
    public function getBlockTypeId()
    {
        return static::BLOCK_ID_IDENTIFICATION;
    }
}