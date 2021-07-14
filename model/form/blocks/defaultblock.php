<?php

namespace tradersoft\model\form\blocks;

class DefaultBlock extends AbstractBlock
{
    /**
     * @inheritDoc
     */
    public function getBlockTypeId()
    {
        return static::BLOCK_ID_DEFAULT;
    }
}