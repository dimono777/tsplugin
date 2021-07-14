<?php

namespace tradersoft\model;

use tradersoft\model\validator\ValidationInterface;
use tradersoft\model\validator\ValidationInternalInterface;

interface ModelWithFieldInterface extends ValidationInterface, ValidationInternalInterface, ModelInterface
{
    public function formName();

    /**
     * @return ModelWithFieldInterface[]
     */
    public function getRelationModels();
}