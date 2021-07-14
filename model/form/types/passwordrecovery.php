<?php

namespace tradersoft\model\form\types;

use tradersoft\helpers\Link;
use tradersoft\interfaces\ISystemMessage;
use tradersoft\model\form\abstracts\AbstractForm;
use tradersoft\model\Token;
use TS_Functions;
use TSInit;

/**
 * Class PasswordRecovery
 *
 * @author  Arseniy Khmelnitskiy <arseniy.khmelnitskiy@tstechpro.com>
 * @package tradersoft\model\form
 *
 */
class PasswordRecovery extends AbstractForm
{
    /**
     * @return bool
     * @throws \Exception
     */
    public function save()
    {
        if (!TSInit::$app->session->has(Token::PASSWORD_RECOVERY_FLASH_KEY)) {
            $this->addSystemMessage(TS_Functions::__('Account not found'), ISystemMessage::SYSTEM_MESSAGE_WARNING);

            return false;
        }

        $result = $this->_send(
            'form-password-recovery',
            $this->getAttributesValues(),
            [
                'leadId' => TSInit::$app->session->get(Token::PASSWORD_RECOVERY_FLASH_KEY),
            ]
        );

        if (!$result->isSuccess()) {
            $this->addSystemMessage($result->getMessage(), ISystemMessage::SYSTEM_MESSAGE_WARNING);

            return false;
        }

        TSInit::$app->session->remove(Token::PASSWORD_RECOVERY_FLASH_KEY);

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
        if (TS_Functions::issetLink('[TS-AFTER-RECOVERY-PASSWORD]')) {
            return Link::getForPageWithKey('[TS-AFTER-RECOVERY-PASSWORD]');
        }
        if (TS_Functions::issetLink('[TS-AUTHORIZATION]')) {
            return Link::getForPageWithKey('[TS-AUTHORIZATION]');
        }
        return TSInit::$app->request->getLink('/');
    }
}