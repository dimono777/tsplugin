<?php

namespace tradersoft\components\redirect_ip_country;

use tradersoft\components\GoogleAnalytics;
use tradersoft\components\redirect_ip_country\model\Settings;
use tradersoft\components\DataPolicyRegistration;
use tradersoft\helpers\Arr;
use tradersoft\helpers\Interlayer_Crm;
use TSInit;

/**
 * Class MainRedirect
 *
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class MainRedirect
{
    /** @var string */
    protected $_redirectDomain;

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return bool
     */
    public function checkRules()
    {
        if (Settings::isNeedRedirect() && $this->_allow()) {
            return $this->_checkDomain();
        }

        return true;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return mixed|string
     */
    public function getDestinationUrl()
    {

        $url = TSInit::$app->request->getPathBase();
        $mainDomain = TSInit::$app->request->getMainDomain();
        if ($this->_redirectDomain && ($this->_redirectDomain !== $mainDomain)) {
            $url = GoogleAnalytics::addClientIdToUrl(
                str_replace($mainDomain, $this->_redirectDomain, $url)
            );
        }

        return $url;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return bool
     */
    protected function _allow()
    {
        return (
            !TSInit::$app->request->isAjax
            && TSInit::$app->trader->isGuest
            && !DataPolicyRegistration::ignoreRedirectByIp()
        );
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return bool
     */
    protected function _checkDomain()
    {

        $mainDomain = TSInit::$app->request->getMainDomain();
        if ($this->_redirectDomain === null) {

            $clientIp = TSInit::$app->request->getUserIP();
            $data = Interlayer_Crm::checkDomainByIp($clientIp, $mainDomain);
            $returnCode = Arr::get($data, 'returnCode', Interlayer_Crm::RESPONSE_CODE_UNSPECIFIED_ERROR);
            if ($returnCode == Interlayer_Crm::RESPONSE_CODE_SUCCESS) {
                $this->_redirectDomain = Arr::get($data, 'validDomain');
            }

            if (!$this->_redirectDomain) {
                $this->_redirectDomain = $mainDomain;
            }
        }

        return ($this->_redirectDomain === $mainDomain);
    }
}