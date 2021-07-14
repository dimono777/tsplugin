<?php

namespace tradersoft\model;

use tradersoft\model\form\blocks\BlockInterface;
use tradersoft\model\validator\ValidationInterface;

interface ModelWithBlockInterface extends ValidationInterface, ModelInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @param int    $index
     *
     * @return BlockInterface
     */
    public function getBlock($name, $index);

    /**
     * @param string $blockName
     *
     * @return BlockInterface[]
     */
    public function getBlocksByName($blockName);

    /**
     * @param int $typeId
     *
     * @return BlockInterface[]
     */
    public function getBlocksByTypeId($typeId);
}