<?php

namespace tradersoft\model\form\types;

use tradersoft\helpers\Interlayer_Crm;
use tradersoft\helpers\Link;
use tradersoft\interfaces\ISystemMessage;
use tradersoft\model\form\abstracts\AbstractForm;
use TS_Functions;
use TSInit;

/**
 * Class EmailForPasswordRecovery
 *
 * @author  Arseniy Khmelnitskiy <arseniy.khmelnitskiy@tstechpro.com>
 * @package tradersoft\model\form
 *
 */
class EmailForPasswordRecovery extends AbstractForm
{
    /**
     * @return bool
     * @throws \Exception
     */
    public function save()
    {
        $result = $this->_send('form-email-for-password-recovery', $this->getAttributesValues());

        if (!$result->isSuccess() && $result->getCode() != Interlayer_Crm::RESPONSE_CODE_TOO_MANY_PASSWORD_RESETS) {
            $this->addSystemMessage($result->getMessage(), ISystemMessage::SYSTEM_MESSAGE_WARNING);
            return false;
        }

        if ($redirectUrl = $this->_getRedirectUrl()) {
            $this->setRedirectUrl($redirectUrl);
        }

        $this->addSystemMessage(TS_Functions::__('Success'), ISystemMessage::SYSTEM_MESSAGE_SUCCESS);

        return true;
    }

    /**
     * @return mixed|string
     */
    protected function _getRedirectUrl()
    {
        if (TS_Functions::issetLink('[TS-AFTER-FORGOT-PASSWORD]')) {
            return Link::getForPageWithKey('[TS-AFTER-FORGOT-PASSWORD]');
        }
        if (TS_Functions::issetLink('[TS-AUTHORIZATION]')) {
            return Link::getForPageWithKey('[TS-AUTHORIZATION]');
        }
        return TSInit::$app->request->getLink('/');
    }
}