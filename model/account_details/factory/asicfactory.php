<?php
namespace tradersoft\model\account_details\factory;

use tradersoft\model\system\AutoVerificationStatuses;
use tradersoft\model\account_details\Base;
use tradersoft\model\account_details\asic\ProcessVerification as ASICProcessVerification;
use tradersoft\model\account_details\asic\AfterVerification as ASICAfterVerification;
use tradersoft\model\account_details\asic\BeforeVerification as ASICBeforeVerification;
use tradersoft\model\factory\SituationInterface;

class ASICFactory implements SituationInterface
{
    /**
     * @return Base
     */
    public static function createModel()
    {
        $status = AutoVerificationStatuses::getStatus();
        if (in_array($status, [
            AutoVerificationStatuses::STATUS_PENDING,
            AutoVerificationStatuses::STATUS_UNSUCCESSFUL_ATTEMPT
        ])) {
            return new ASICProcessVerification();
        } elseif(in_array($status, [
            AutoVerificationStatuses::STATUS_NOT_VERIFIED,
            AutoVerificationStatuses::STATUS_VERIFIED,
            AutoVerificationStatuses::STATUS_MANUALLY_VERIFIED
        ])) {
            return new ASICAfterVerification();
        } else {
            return new ASICBeforeVerification();
        }
    }
}