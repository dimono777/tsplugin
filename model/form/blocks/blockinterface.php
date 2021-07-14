<?php

namespace tradersoft\model\form\blocks;

use tradersoft\model\form\fields\FieldInterface;
use tradersoft\model\ModelWithBlockInterface;
use tradersoft\model\ModelWithFieldInterface;

interface BlockInterface extends ModelWithFieldInterface
{
    const BLOCK_ID_DEFAULT = 0;
    const BLOCK_ID_COMMON = 1;
    const BLOCK_ID_REPEATING = 2;
    const BLOCK_ID_AVAILABLE = 3;
    const BLOCK_ID_IDENTIFICATION = 4;

    const BLOCK_ATTR_CLASS = 'class';
    const BLOCK_ATTR_REPEAT_CNT = 'repeatCnt';
    const BLOCK_ATTR_REPEAT_BTN_TITLE = 'repeatButtonTitle';

    public function getName();

    /**
     * @return int
     */
    public function getBlockId();

    /**
     * @return int
     */
    public function getBlockTypeId();

    /**
     * @param int $index
     */
    public function setIndex($index);

    /**
     * @return int
     */
    public function getIndex();

    /**
     * @return FieldInterface[]
     */
    public function getFields();

    /**
     * @param FieldInterface $field
     *
     * @return mixed
     */
    public function addField(FieldInterface $field);

    /**
     * @param $blockAttributeName
     *
     * @return mixed
     */
    public function getBlockAttribute($blockAttributeName);

    /**
     * @return bool
     */
    public function isRepeatable();

    /**
     * @return array
     */
    public function getViewOptions();

    /**
     * @param ModelWithBlockInterface $model
     *
     * @return mixed
     */
    public function setParentModel(ModelWithBlockInterface $model);
}