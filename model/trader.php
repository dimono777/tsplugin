<?php
namespace tradersoft\model;

use tradersoft\helpers\HeaderSetting;
use TSInit;
use tradersoft\helpers\Arr;
use tradersoft\helpers\Config;
use tradersoft\helpers\Currency;
use tradersoft\helpers\Interlayer_Crm;
use tradersoft\helpers\Trader_Auth_Cookie;

/**
 * Trader
 * @property bool $isGuest Trader is a guest
 * @property string $fullName Trader full name
 * @property float $balance Trader balance
 * @property string $accountNumber Trader crmHashId
 * @property string $country Trader country
 * @property string $autoVerificationStatus Trader Verification status
 * @property string $autoVerificationBeforeFtd Trader Verification Trigger
 * @property string $deposited Trader was there a deposit
 * @property bool $professionalSuitabilityAvailable Can show Professional Level page
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Trader
{
    const TERMS_STATUS_ACCEPTED = 1;
    const TERMS_STATUS_DECLINED = 2;
    const TERMS_STATUS_NOT_SEEN = 3;
    const TERMS_STATUS_WRONG_COUNTRY_TYPE = 4;
    const TERMS_STATUS_COUNTRY_TYPE_CHANGED = 5;

    const STATUS_VERIFIED = 1;

    protected $autoAuth;

    protected static $_instance;

    private $_info;

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __get($name)
    {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        throw new \Exception('Getting unknown property: ' . get_class($this) . '::' . $name);
    }

    /**
     * Returns a field from trader info.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->_info, $key, $default);
    }

    /**
     * Check if trader is  not logged in.
     *
     * @return bool
     */
    public function getIsGuest()
    {
        return !(bool) \TSInit::$app->session->get('user');
    }

    /**
     * Login trader
     * @param $traderInfo array
     * @param $rememberMe bool
     * @return bool
     */
    public function login(array $traderInfo, $rememberMe = Trader_Auth_Cookie::DEFAULT_REMEMBER_ME_VALUE)
    {
        if (empty($traderInfo)) {
            return false;
        }

        $this->_info = $traderInfo;

        Trader_Auth_Cookie::set($traderInfo, $rememberMe);

        // Update session
        \TSInit::$app->session->set('user', $this->_info);

        return true;
    }

    /**
     * Logout.
     *
     * @return bool
     */
    public function logout()
    {
        if (\TSInit::$app->session->has('user')) {
            \TSInit::$app->session->remove('user');
        }

        $this->_info = [];

        Trader_Auth_Cookie::clear();

        return true;
    }

    /**
     * Update trader info data
     * @param int $crmTraderId
     * @return array|false
     */
    public function updateTraderInfo($crmTraderId = 0)
    {
        if ($this->isGuest) {
            return false;
        }

        $crmTraderId = ($crmTraderId) ? :$this->get('crmId');
        if (!$crmTraderId) {
            $this->logout();
            return false;
        }

        $traderInfo = Interlayer_Crm::getLeadInfo($crmTraderId);
        if (empty ($traderInfo['leadInfo'])) {
            $this->logout();
            return false;
        }

        foreach ($traderInfo['leadInfo'] as $key => $value) {
            $this->_info[$key] = $value;
        }

        \TSInit::$app->session->remove('user');
        \TSInit::$app->session->set('user', $this->_info);

        return $this->_info;
    }

    /**
     * Get trader full name
     * @return string|false;
     */
    public function getFullName()
    {
        if ($this->isGuest) {
            return false;
        }

        $fullName = trim($this->get('fname', '') . ' ' . $this->get('lname', ''));

        return $fullName;
    }

    /**
     * Get trader balance
     * @return float;
     */
    public function getBalance()
    {
        return Currency::getInstance()
            ->formatValue($this->get('balance', 0));
    }

    /**
     * @return string
     */
    public function getFinanceInfoTypeValue()
    {
        if (HeaderSetting::getCurrentFinanceInfoTypeId() === HeaderSetting::FINANCE_INFO_TYPE_BALANCE) {
            return $this->getBalance();
        }

        return '-';
    }

    /**
     * Get trader account number
     * @return string;
     */
    public function getAccountNumber()
    {
        return $this->get('crmHashId');
    }

    /**
     * Get trader country
     * @return string;
     */
    public function getCountry()
    {
        return $this->get('country', '');
    }

    /**
     * Get lead country code
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return string $countryCode
     */
    public function getCountryCode()
    {
        $countryCode = $this->get('country', '');
        if (empty($countryCode)) {
            // country by ip
            $countryByIp = Interlayer_Crm::getCountryByIP(TSInit::$app->request->userIP);
            if (!empty($countryByIp['country_code'])) {
                $countryCode = $countryByIp['country_code'];
            }
        }

        return $countryCode;
    }

    /**
     * Trader verification status
     * @return integer
     */
    public function getAutoVerificationStatus()
    {
        return (int)$this->get('autoVerificationStatus');
    }

    /**
     * Trader verification Trigger
     * @return integer
     */
    public function getAutoVerificationBeforeFtd()
    {
        return (bool)$this->get('autoVerificationBeforeFtd');
    }

    /**
     * Trader was there a deposit
     * @return bool
     */
    public function getDeposited()
    {
        return (bool)$this->get('deposited');
    }

    /**
     * Can show categorisation
     *
     * @return bool
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public function getProfessionalSuitabilityAvailable()
    {
        return (bool) $this->get('professionalSuitabilityAvailable', false);
    }

    /**
     * Function quizPassed
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param mixed $results
     *
     * @return bool
     */

    public function quizPassed($results)
    {
        if ($this->isGuest || !$results) {
            return false;
        }

        /** @var array $response */
        $response = Interlayer_Crm::quizPassed($this->get('crmId'), $results);

        return (
            $response
            && isset($response['returnCode'])
            && $response['returnCode'] == Interlayer_Crm::RESPONSE_CODE_SUCCESS
        );
    }

    /**
     * Function quizAttemptsLimitExceeded
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return bool
     */
    public function quizAttemptsLimitExceeded()
    {
        if ($this->isGuest) {
            return false;
        }

        /** @var array $response */
        $response = Interlayer_Crm::quizAttemptsLimitExceeded($this->get('crmId'));

        return (
            $response
            && isset($response['returnCode'])
            && $response['returnCode'] == Interlayer_Crm::RESPONSE_CODE_SUCCESS
        );
    }

    /**
     * Login by cookie
     * @return bool
     */
    protected function _loginByCookie()
    {
        if (!$this->isGuest) {
            return false;
        }

        if (!Trader_Auth_Cookie::isValid()) {
            Trader_Auth_Cookie::clear();
            return false;
        }

        $traderInfo = Interlayer_Crm::loginByCookies(
            Trader_Auth_Cookie::get()
        );

        if ($traderInfo) {
            $traderInfo = json_decode($traderInfo, true);
            if (
                isset($traderInfo['returnCode'])
                && $traderInfo['returnCode'] == Interlayer_Crm::RESPONSE_CODE_SUCCESS
                && isset($traderInfo['leadInfo'])
            ) {
                return $this->login($traderInfo['leadInfo']);
            }
        }

        Trader_Auth_Cookie::clear();

        return false;
    }

    private function __construct()
    {
        $this->autoAuth = Config::get('trader.autoAuth', true);

        \TS_Functions::initNotAuthedUID();

        $this->_tryLogin(\TSInit::$app->session->get('user'));
    }

    /**
     * Trying login user
     * @param array $traderInfo
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    protected function _tryLogin($traderInfo)
    {
        if (!Trader_Auth_Cookie::checkAuthCookie()) {
            $this->logout();
            return;
        }

        if (!empty($traderInfo)) {
            if (Trader_Auth_Cookie::isValid()) {
                $this->_info = $traderInfo;
                // update cookie (for extend cookie life time)
                Trader_Auth_Cookie::set(
                    $traderInfo,
                    Arr::get(
                        Trader_Auth_Cookie::get(), Trader_Auth_Cookie::REMEMBER_ME,
                        Trader_Auth_Cookie::DEFAULT_REMEMBER_ME_VALUE
                    )
                );
            } else {
                $this->logout();
            }
        } elseif($this->autoAuth) {
            $this->_loginByCookie();
        }
    }
}