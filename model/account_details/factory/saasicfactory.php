<?php
namespace tradersoft\model\account_details\factory;

use tradersoft\model\system\AutoVerificationStatuses;
use tradersoft\model\account_details\Base;
use tradersoft\model\account_details\saasic\ProcessVerification as SAASICProcessVerification;
use tradersoft\model\account_details\saasic\AfterVerification as SAASICAfterVerification;
use tradersoft\model\account_details\saasic\BeforeVerification as SAASICBeforeVerification;
use tradersoft\model\factory\SituationInterface;

class SAASICFactory implements SituationInterface
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
            return new SAASICProcessVerification();
        } elseif(in_array($status, [
            AutoVerificationStatuses::STATUS_NOT_VERIFIED,
            AutoVerificationStatuses::STATUS_VERIFIED,
            AutoVerificationStatuses::STATUS_MANUALLY_VERIFIED
        ])) {
            return new SAASICAfterVerification();
        } else {
            return new SAASICBeforeVerification();
        }
    }
}