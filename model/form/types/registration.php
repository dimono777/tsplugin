<?php
namespace tradersoft\model\form\types;

use tradersoft\helpers\Arr;
use tradersoft\interfaces\ISystemMessage;
use tradersoft\model\Base_Registration;
use tradersoft\model\form\abstracts\AbstractForm;
use tradersoft\model\redirect_after_action\actions\Registration as ActionRegistration;
use TS_Functions;

/**
 * Class Registration
 * @package tradersoft\model\form
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Registration extends AbstractForm
{
    /**
     * @return bool
     * @throws \Exception
     */
    public function save()
    {
        $result = $this->_send('form-create-account', $this->getAttributesValues(), $this->_getCookieParams());

        if (!$result->isSuccess()) {
            $this->addSystemMessage($result->getMessage(), ISystemMessage::SYSTEM_MESSAGE_WARNING);
            return false;
        }

        if (($redirectUrl = Arr::get($result->getData(), 'redirectUrl'))) {
            $this->setRedirectUrl($this->_addFlagToForceLoginLink($redirectUrl));
        }

        $this->addSystemMessage(TS_Functions::__('Success'), ISystemMessage::SYSTEM_MESSAGE_SUCCESS);

        return true;
    }

    /**
     * Get param for registration from cookies
     * @return array
     */
    protected function _getCookieParams()
    {
        $cookieParams = [];
        $checkParams = [
            'aff_id'     => [
                'cookies' => ['aff_id', 'olgs_aff'],
                'default' => 0,
            ],
            'str_id'     => [
                'cookies' => ['str_id', 'str', 'olgs_str'],
                'default' => '',
            ],
            'tr_id'      => [
                'cookies' => ['tr_id', 'tr', 'olgs_tr'],
                'default' => '',
            ],
            'cmp_id'     => [
                'cookies' => ['cmp_id', 'olgs_cmp'],
                'default' => 0,
            ],
            'aff_cmp_id' => [
                'cookies' => ['acmp_id', 'olgs_acmp'],
                'default' => 0,
            ],
            'b_id'       => [
                'cookies' => ['banner_id', 'b', 'bid', 'olgs_g'],
                'default' => '',
            ],
            'mob_token'       => [
                'cookies' => ['mob_token'],
                'default' => '',
            ],
            'p4r_reqid'       => [
                'cookies' => ['p4r_reqid'],
                'default' => '',
            ],
            'sep_qs'       => [
                'cookies' => ['sep_qs'],
                'default' => 0,
            ],
            'at'       => [
                'cookies' => ['at'],
                'default' => '',
            ],
            'ref_url'       => [
                'cookies' => ['ref_url'],
                'default' => '',
            ],
            'srkey'       => [
                'cookies' => ['srkey'],
                'default' => '',
            ],
            'referrerKey'       => [
                'cookies' => ['referrerKey'],
                'default' => '',
            ],
            'promote_id'       => [
                'cookies' => ['promote_id'],
                'default' => '',
            ],
            'ref_key'       => [
                'cookies' => ['ref_key'],
                'default' => '',
            ],
            'newsletter_tr'       => [
                'cookies' => ['newsletter_tr'],
                'default' => '',
            ],
            'gclid'       => [
                'cookies' => ['gclid'],
                'default' => '',
            ],
            'cl_id'       => [
                'cookies' => ['cl_id'],
                'default' => 0,
            ],
            'click_id'       => [
                'cookies' => ['click_id'],
                'default' => 0,
            ],
        ];

        foreach ($checkParams as $paramName => $cookies) {
            $cookieParams[$paramName] = $cookies['default'];

            foreach ($cookies['cookies'] as $cookie) {
                $value = Arr::get($_COOKIE, $cookie);
                if (!is_null($value)) {
                    $cookieParams[$paramName] = $value;
                    break;
                }
            }
        }

        return $cookieParams;
    }


    /**
     * Add a flag to the force login link, so that the force login page, on another site,
     * would understand that they came to her after registration
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param string $link
     *
     * @return string
     */
    protected function _addFlagToForceLoginLink($link)
    {
        return add_query_arg([Base_Registration::CAME_AFTER_ACTION => ActionRegistration::ID], $link);
    }

}