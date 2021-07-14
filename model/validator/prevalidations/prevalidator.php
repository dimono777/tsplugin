<?php

namespace tradersoft\model\validator\prevalidations;

use tradersoft\helpers\Arr;
use tradersoft\model\ModelWithFieldInterface;
use tradersoft\model\validator\prevalidations\conditions\ConditionInterface;
use tradersoft\model\validator\prevalidations\conditions\FieldHasValue;

class PreValidator
{
    const CONDITION_KEY_NAME = 'name';
    const CONDITION_KEY_PARAMS = 'params';

    protected $_model;

    /**
     * PreValidator constructor.
     *
     * @param ModelWithFieldInterface $model
     */
    public function __construct(ModelWithFieldInterface $model)
    {
        $this->_model = $model;
    }

    /**
     * @param array $conditions
     *
     * @return bool
     * @throws \Exception
     */
    public function check(array $conditions)
    {
        foreach ($conditions as $conditionData) {
            if (!($conditionName = Arr::get($conditionData, static::CONDITION_KEY_NAME))) {
                throw new \Exception('Incorrect conditions data');
            }
            if (!$this->_checkCondition($conditionName, Arr::get($conditionData, static::CONDITION_KEY_PARAMS))) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $conditionName
     * @param array  $params
     *
     * @return bool
     * @throws \Exception
     */
    protected function _checkCondition($conditionName, array $params = [])
    {
        return $this->_getCondition($conditionName)->check($params);
    }

    /**
     * @param $conditionName
     *
     * @return ConditionInterface
     * @throws \Exception
     */
    protected function _getCondition($conditionName)
    {
        switch ($conditionName) {
            case FieldHasValue::NAME:
                return new FieldHasValue($this->_model);
        }
        throw new \Exception("Unknown condition name. [conditionName = $conditionName]");
    }
}