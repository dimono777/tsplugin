<?php

namespace tradersoft\model\form\blocks;

use tradersoft\model\form\decorators\CRMStructureBlock;

class Factory
{
    /**
     * @param int   $blockTypeId
     * @param array $fieldsData
     * @param CRMStructureBlock $blockStructure
     *
     * @return BlockInterface
     * @throws \Exception
     */
    public static function getBlock($blockTypeId, array $fieldsData, CRMStructureBlock $blockStructure)
    {
        switch ($blockTypeId) {
            case BlockInterface::BLOCK_ID_COMMON:
                return new Common($fieldsData, $blockStructure);
            case BlockInterface::BLOCK_ID_REPEATING:
                return new Repeating($fieldsData, $blockStructure);
            case BlockInterface::BLOCK_ID_AVAILABLE:
                return new Available($fieldsData, $blockStructure);
            case BlockInterface::BLOCK_ID_IDENTIFICATION:
                return new Identification($fieldsData, $blockStructure);
            case BlockInterface::BLOCK_ID_DEFAULT:
                return new DefaultBlock($fieldsData, $blockStructure);
        }

        throw new \Exception("Unknown block type id. [blockTypeId=$blockTypeId]");
    }
}