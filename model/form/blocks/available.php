<?php

namespace tradersoft\model\form\blocks;

class Available extends AbstractBlock
{
    /**
     * @inheritDoc
     */
    public function getBlockTypeId()
    {
        return static::BLOCK_ID_AVAILABLE;
    }
}