<?php

namespace tradersoft\model\form\decorators;

use Exception;

class FormAdditionalParam
{
    const FIELD_PARAM_ID = 'paramId';
    const FIELD_VALUE = 'valueParse';

    const PARAM_ID_FOR_ISLAMIC = 1;
    const PARAM_ID_CREATE_EXTERNAL_ACCOUNT = 2;
    const PARAM_ID_REGISTRATION_SITE_TITLE = 3;
    const PARAM_ID_WITH_CAPTCHA = 4;
    const PARAM_ID_AML_SITE_TITLE = 5;
    const PARAM_ID_REGISTRATION_DISABLE_DEFAULT_STYLES = 6;
    const PARAM_ID_AML_DISABLE_DEFAULT_STYLES = 7;

    const PARAM_ID_EMAIL_PASSWORD_RECOVERY_SITE_TITLE = 8;
    const PARAM_ID_EMAIL_PASSWORD_RECOVERY_INVISIBLE_RECAPTCHA = 9;
    const PARAM_ID_EMAIL_PASSWORD_RECOVERY_DISABLE_DEFAULT_STYLES = 10;

    const PARAM_ID_PASSWORD_RECOVERY_SITE_TITLE = 11;
    const PARAM_ID_PASSWORD_RECOVERY_DISABLE_DEFAULT_STYLES = 12;

    const PARAM_ID_CONTACT_US_SITE_TITLE = 13;
    const PARAM_ID_CONTACT_US_INVISIBLE_RECAPTCHA = 14;
    const PARAM_ID_CONTACT_US_DISABLE_DEFAULT_STYLES = 15;

    protected $_data;

    /**
     * FormAdditionalParam constructor.
     *
     * @param array $data
     *
     * @throws Exception
     */
    public function __construct(array $data)
    {
        if (!$this->_checkData($data)) {
            throw new Exception('Incorrect additional param data');
        }

        $this->_data = $data;
    }

    public function getParamId()
    {
        return $this->_data[static::FIELD_PARAM_ID];
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->_data[static::FIELD_VALUE];
    }

    protected function _checkData(array $data)
    {
        return array_key_exists(static::FIELD_PARAM_ID, $data) && array_key_exists(static::FIELD_VALUE, $data);
    }
}