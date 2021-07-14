<?php

namespace tradersoft\model\validator\prevalidations\conditions;

use tradersoft\model\ModelWithFieldInterface;

abstract class AbstractCondition implements ConditionInterface
{
    protected $_model;

    /**
     * AbstractCondition constructor.
     *
     * @param ModelWithFieldInterface $model
     */
    public function __construct(ModelWithFieldInterface $model)
    {
        $this->_model = $model;
    }
}