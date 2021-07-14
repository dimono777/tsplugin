<?php

namespace tradersoft\model;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Interlayer_Crm;
use tradersoft\helpers\system\Translate;

/**
 * Trader forgot password model
 */
class Trader_Forgot_Password extends Partners_Forgot_Password
{
    public function send()
    {
        $data = Interlayer_Crm::tryForgotPassword($this->email);

        $result = false;

        if ($data) {
            $returnCode = Arr::get($data, 'returnCode', Interlayer_Crm::RESPONSE_CODE_UNSPECIFIED_ERROR);

            if ($returnCode == Interlayer_Crm::RESPONSE_CODE_SUCCESS) {
                $result = true;
            } else {
                $this->addError('email', Translate::__($data['description']));
            }
        }

        return $result;
    }
}