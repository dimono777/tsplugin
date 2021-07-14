<?php


namespace tradersoft\model\validator\prevalidations\conditions;


interface ConditionInterface
{
    /**
     * @param array $params
     *
     * @return bool
     */
    public function check(array $params);
}