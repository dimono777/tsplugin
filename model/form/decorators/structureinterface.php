<?php

namespace tradersoft\model\form\decorators;

interface StructureInterface
{
    const FIELD_ID = 'id';
    const FIELD_TYPE = 'type';
    const FIELD_NAME = 'name';
    const FIELD_ADDITIONAL_PARAMS = 'additionalParams';
    const FIELD_FIELDS = 'fields';
    const FIELD_BLOCKS = 'blocks';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return array
     */
    public function getFields();

    /**
     * @return CRMStructureBlock[]
     */
    public function getBlocks();

    /**
     * @param int $blockId
     *
     * @return CRMStructureBlock|null
     */
    public function getBlock($blockId);

    /**
     * @param int $blockId
     *
     * @return array
     */
    public function getBlockFields($blockId);

    /**
     * @return FormAdditionalParam[]
     */
    public function getAdditionalParams();

    /**
     * @param $paramId
     *
     * @return FormAdditionalParam|null
     */
    public function getAdditionalParam($paramId);

    /**
     * @return string
     */
    public function getSiteTitle();

    /**
     * @return bool
     */
    public function isDisableDefaultStyles();
}