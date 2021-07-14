<?php

namespace tradersoft\model\form\blocks;

class Repeating extends AbstractBlock
{

    /**
     * @inheritDoc
     */
    public function getBlockTypeId()
    {
        return static::BLOCK_ID_REPEATING;
    }
}