<?php
namespace tradersoft\model\account_details\asic;

use tradersoft\model\account_details\GBG;
use TSInit;

class Base extends GBG
{
    public $middleName;

    protected function _loadModelAttributes()
    {
        parent::_loadModelAttributes();
        $this->middleName = TSInit::$app->trader->get('middleName');
    }
}