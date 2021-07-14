<?php

namespace tradersoft\model\verification\helpers;

use tradersoft\helpers\Arr;
use TS_Functions;

class ErrorMessages
{
    const MESSAGES = [
        'not_empty' => 'Сan\'t be empty',
        'not_both_empty' => 'Сan\'t be empty',
        'amlVerification_requiredProofCheckbox' => 'You must read and agree to the above',
    ];

    /**
     * @param string $error
     * @param bool $withTranslation
     * @return string
     */
    public static function getMessage($error, $withTranslation = false)
    {
        $errorMessage = Arr::get(self::MESSAGES, $error, 'Invalid value');
        if ($withTranslation && $errorMessage) {
            $errorMessage = TS_Functions::__($errorMessage);
        }

        return $errorMessage;
    }

}