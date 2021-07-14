<?php
namespace tradersoft\model\account_details\factory;

use tradersoft\model\account_details\Base;
use tradersoft\model\account_details\Native;
use tradersoft\model\factory\SituationInterface;
use tradersoft\helpers\RegulationSetting;

final class AccountDetailsFactory implements SituationInterface
{
    /**
     * @return Base
     */
    public static function createModel()
    {
        switch (RegulationSetting::getType()) {
            case RegulationSetting::EDIT_PROFILE_TYPE_ASIC:
                $model = ASICFactory::createModel();
                break;
            case RegulationSetting::EDIT_PROFILE_TYPE_SAASIC:
                $model = SAASICFactory::createModel();
                break;
            default:
                $model = new Native();
                break;
        }

        return $model;
    }
}