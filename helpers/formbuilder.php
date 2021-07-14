<?php

namespace tradersoft\helpers;

use tradersoft\helpers\system\PageKey;
use tradersoft\model\form\decorators\CRMStructure;

class FormBuilder
{
    const PARAM_KEY_TYPE = 'type';

    const TYPE_REGISTRATION = CRMStructure::TYPE_REGISTRATION;
    const TYPE_AML_VERIFICATION = CRMStructure::TYPE_AML_VERIFICATION;
    const TYPE_EMAIL_FOR_PASSWORD_RECOVERY = CRMStructure::TYPE_EMAIL_FOR_PASSWORD_RECOVERY;
    const TYPE_PASSWORD_RECOVERY = CRMStructure::TYPE_PASSWORD_RECOVERY;
    const TYPE_CONTACT_US = CRMStructure::TYPE_CONTACT_US;

    /**
     * @param array $params
     *
     * @return string
     * @throws \Exception
     */
    public static function getShortCodeRegistration(array $params = [])
    {
        $params = static::prepareShortCodeParams(static::TYPE_REGISTRATION, $params);

        return PageKey::getPageShortCode(PageKey::KEY_FORMS, $params);
    }

    /**
     * @param array $params
     *
     * @return string
     * @throws \Exception
     */
    public static function getShortCodeAMLVerification(array $params = [])
    {
        $params = static::prepareShortCodeParams(static::TYPE_AML_VERIFICATION, $params);

        return PageKey::getPageShortCode(PageKey::KEY_FORMS, $params);
    }

    /**
     * @param array $params
     *
     * @return string
     * @throws \Exception
     */
    public static function getShortCodeEmailForPasswordRecovery(array $params = [])
    {
        $params = static::prepareShortCodeParams(static::TYPE_EMAIL_FOR_PASSWORD_RECOVERY, $params);

        return PageKey::getPageShortCode(PageKey::KEY_FORMS, $params);
    }

    /**
     * @param array $params
     *
     * @return string
     * @throws \Exception
     */
    public static function getShortCodePasswordRecovery(array $params = [])
    {
        $params = static::prepareShortCodeParams(static::TYPE_PASSWORD_RECOVERY, $params);

        return PageKey::getPageShortCode(PageKey::KEY_FORMS, $params);
    }

    /**
     * @param array $params
     *
     * @return string
     * @throws \Exception
     */
    public static function getShortCodeContactUs(array $params = [])
    {
        $params = static::prepareShortCodeParams(static::TYPE_CONTACT_US, $params);

        return PageKey::getPageShortCode(PageKey::KEY_FORMS, $params);
    }

    /**
     * @param       $type
     * @param array $params
     *
     * @return array
     */
    public static function prepareShortCodeParams($type , array $params)
    {
        $params[static::PARAM_KEY_TYPE] = $type;

        return $params;
    }

}
