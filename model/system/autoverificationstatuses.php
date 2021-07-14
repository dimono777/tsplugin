<?php
namespace tradersoft\model\system;

use TSInit;
use tradersoft\helpers\RegulationSetting;

/**
 * Model for working with the status of the traders's auto verification
 */
class AutoVerificationStatuses
{
    const STATUS_NOT_APPLICABLE       = 0;
    const STATUS_PENDING              = 1;
    const STATUS_UNSUCCESSFUL_ATTEMPT = 2;
    const STATUS_NOT_VERIFIED         = 3;
    const STATUS_VERIFIED             = 4;
    const STATUS_MANUALLY_VERIFIED    = 5;

    /**
     * Get auto verification status of trader
     * @return int||null
     */
    public static function getStatus()
    {
        $status = TSInit::$app->trader->autoVerificationStatus;
        if (
            in_array($status, [
                self::STATUS_PENDING,
                self::STATUS_UNSUCCESSFUL_ATTEMPT,
                self::STATUS_NOT_VERIFIED,
                self::STATUS_VERIFIED,
                self::STATUS_MANUALLY_VERIFIED,
            ])
        ) {
            return $status;
        }

        return null;
    }

    /**
     * @return bool
     */
    public static function isStatusVerify()
    {
        if (
            in_array(self::getStatus(), [
                self::STATUS_VERIFIED,
                self::STATUS_MANUALLY_VERIFIED
            ])
        ) {
            return true;
        }

        return false;
    }

    public static function isProcessVerification()
    {
        return RegulationSetting::getType() == RegulationSetting::EDIT_PROFILE_TYPE_ASIC
            && in_array(self::getStatus(), [
                AutoVerificationStatuses::STATUS_PENDING,
                AutoVerificationStatuses::STATUS_UNSUCCESSFUL_ATTEMPT
            ]);
    }
}