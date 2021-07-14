<?php

namespace tradersoft\model\form\types;

use tradersoft\interfaces\ISystemMessage;
use tradersoft\model\form\abstracts\AbstractForm;
use TS_Functions;

/**
 * Class ContactUs
 *
 * @author  Arseniy Khmelnitskiy <arseniy.khmelnitskiy@tstechpro.com>
 * @package tradersoft\model\form
 *
 */
class ContactUs extends AbstractForm
{
    /**
     * @return bool
     * @throws \Exception
     */
    public function save()
    {
        $result = $this->_send('form-contact-us', $this->getAttributesValues());

        if (!$result->isSuccess()) {
            $this->addSystemMessage($result->getMessage(), ISystemMessage::SYSTEM_MESSAGE_WARNING);

            return false;
        }

        $this->addSystemMessage(TS_Functions::__('The information was successfully sent'), ISystemMessage::SYSTEM_MESSAGE_SUCCESS);

        return true;
    }
}