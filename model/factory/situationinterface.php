<?php
namespace tradersoft\model\factory;
use tradersoft\model\Model;

interface SituationInterface
{
    /**
     * @return Model
     */
    public static function createModel();
}