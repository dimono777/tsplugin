<?php
namespace tradersoft\model;

use tradersoft\helpers\Interlayer_Crm;
use tradersoft\helpers\Arr;
use tradersoft\helpers\multi_language\Multi_Language;
use tradersoft\helpers\TS_Setting;
use TSInit;
use tradersoft\model\redirect_after_action\Init as RedirectAfterActionInit;
use tradersoft\model\redirect_after_action\actions\Registration as RedirectAfterRegistration;

/**
 * Base registration model
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
abstract class Base_Registration extends ModelWithCaptcha
{
    // Registration key for force login
    const CAME_AFTER_ACTION = 'after_action';

    const FIELD_ACCEPT_TC = 'accept';
    const FIELD_NOT_US_REPORTABLE_PERSON = 'notUSReportablePerson';
    const FIELD_AGREED_RECEIVE_NEWS_LETTERS = 'agreedReceiveNewsletters';
    const FIELD_AGREED_PRIVACY_POLICY = 'agreedPrivacyPolicy';
    const FIELD_CREATE_EXTERNAL_ACCOUNT = 'createExternalAccount';

    public $fname;
    public $lname;
    public $country;
    public $phone;
    public $phoneCode;
    public $email;
    public $password;
    public $confirmPassword;
    public $accept;
    public $promoCode;
    public $currency;
    public $notUSReportablePerson;
    public $agreedReceiveNewsletters;
    public $agreedPrivacyPolicy;
    public $createExternalAccount;

    protected $_formId = 'form_sign_up';
    protected $_withCurrency = false;
    protected $_withNotUSReportablePerson = false;
    protected $_withReceiveEmailNewslettersAgreement = false;
    protected $_withCreateExternalAccount = '';
    protected $_withPrivacyPolicy = false;

    protected static $_cache;

    public function save()
    {
        $this->_beforeSave();
        if ($this->hasErrors()) {
            return false;
        }

        $data = $this->_registration();
        if (!$data) {
            return false;
        }

        $data = json_decode($data);
        $returnCode = isset($data->{'returnCode'}) ?
            $data->{'returnCode'}
            : Interlayer_Crm::RESPONSE_CODE_UNSPECIFIED_ERROR;

        if ($returnCode != Interlayer_Crm::RESPONSE_CODE_SUCCESS) {
            $this->_prepareError($data);
        }

        $result['data'] = $data;
        $result['isRegister'] = $returnCode == Interlayer_Crm::RESPONSE_CODE_SUCCESS ? true : false;
        if (
            !\TSInit::$app->request->isLocal
            && isset($data->{'redirectUrl'})
            && $this->_isForceRedirect($data->{'redirectUrl'})
        ) {
            $result['redirectTo'] = $data->{'redirectUrl'};
        }

        return $result;
    }

    /**
     * Auth by user name
     * @return array|false
     */
    public function auth()
    {
        $data = $this->_auth();
        if (!$data) {
            return false;
        }

        $data = json_decode($data);
        $returnCode = isset($data->{'returnCode'}) ?
            $data->{'returnCode'}
            : Interlayer_Crm::RESPONSE_CODE_UNSPECIFIED_ERROR;
        $result['data']         = $data;
        $result['isAuth']       = $returnCode == Interlayer_Crm::RESPONSE_CODE_SUCCESS ? true : false;

        //RESPONSE_CODE_WRONG_REGION
        if ($returnCode == Interlayer_Crm::RESPONSE_CODE_WRONG_REGION) {
            $result['redirectTo'] = TSInit::$app->request->getLink('/');
        }

        //RESPONSE_CODE_WRONG_DOMAIN
        if (
            $returnCode == Interlayer_Crm::RESPONSE_CODE_WRONG_DOMAIN
            && isset($data->{'leadInfo'}->{'redirectTo'})
        ) {
            $result['redirectTo'] = $data->{'leadInfo'}->{'redirectTo'};
        }

        return $result;
    }

    public function getCountriesList($showAllCountries = false, $filterByCountryType = false)
    {
        $countriesData = $this->_getCountries($filterByCountryType);
        if (!empty($countriesData['allCountries'])) {
            if ($showAllCountries) {
                return $countriesData['allCountries'];
            }
            unset($countriesData['allCountries']);
        }

        $countriesList = [];
        foreach ($countriesData as $val) {
            $countriesList = array_merge($countriesList, $val);
        }
        asort($countriesList, SORT_NATURAL | SORT_FLAG_CASE);

        return $countriesList;
    }

    /**
     * Prepare options for countries list
     * @param $showAllCountries bool
     * @param $filterByCountryType bool
     * @return array
     */
    public function getCountriesOptions($showAllCountries = false, $filterByCountryType = false)
    {
        $options=[];
        $countries = $this->getCountriesList($showAllCountries, $filterByCountryType);
        $countriesByType = $this->getCountriesList(false, $filterByCountryType);
        $phonesCode = $this->_preparePhoneCode();

        foreach (array_keys($countries) as $key) {
            $options[$key] = Arr::get($phonesCode, $key);
            if ($showAllCountries && empty($countriesByType[$key])) {
                $options[$key]['data-invalid'] = 1;
                //$options[$key]['disabled'] = 'disabled';
            }
        }

        return $options;
    }

    /**
     * @param $withCurrency bool
     */
    public function setWithCurrency($withCurrency)
    {
        $this->_withCurrency = (bool)$withCurrency;
        $this->_setCurrency();
    }

    /**
     * @return bool
     */
    public function withCurrency()
    {
        return $this->_withCurrency;
    }

    /**
     * @param $withUSReportablePerson bool
     */
    public function setWithNotUSReportablePerson($withUSReportablePerson)
    {
        $this->_withNotUSReportablePerson = (bool)$withUSReportablePerson;
    }

    /**
     * @return bool
     */
    public function withNotUSReportablePerson()
    {
        return $this->_withNotUSReportablePerson;
    }

    /**
     * Get currency list
     * @return array
     */
    public function getCurrencyList()
    {
        $currenciesData = $this->_getCurrencies();
        return Arr::map(Arr::get($currenciesData, 'allCurrencies', []), 'code', 'code');
    }

    /**
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param mixed $receiveEmailNewslettersAgreement
     */
    public function setWithReceiveEmailNewslettersAgreement($receiveEmailNewslettersAgreement)
    {
        $this->_withReceiveEmailNewslettersAgreement = (bool) $receiveEmailNewslettersAgreement;
    }

    /**
     * @param string $withCreateExternalAccount
     */
    public function setWithCreateExternalAccount($withCreateExternalAccount)
    {
        $this->_withCreateExternalAccount = (string) $withCreateExternalAccount;
    }

    /**
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @return bool
     */
    public function withReceiveEmailNewslettersAgreement()
    {
        return $this->_withReceiveEmailNewslettersAgreement;
    }

    /**
     * @return string
     */
    public function withCreateExternalAccount()
    {
        return $this->_withCreateExternalAccount;
    }

    /**
     * @param $withPrivacyPolicy bool
     */
    public function setWithPrivacyPolicy($withPrivacyPolicy)
    {
        $this->_withPrivacyPolicy = (bool)$withPrivacyPolicy;
    }

    /**
     * @return bool
     */
    public function withPrivacyPolicy()
    {
        return $this->_withPrivacyPolicy;
    }

    /**
     * Get countries list
     * @param $filterByCountryType bool
     * @return array
     */
    protected function _getCountries($filterByCountryType = false)
    {
        if (!isset(self::$_cache['countriesData'][$filterByCountryType])) {

            self::$_cache['countriesData'][$filterByCountryType] = Interlayer_Crm::getCountriesByCountryTypes(
                null,
                true,
                true,
                $filterByCountryType
            );
        }

        return self::$_cache['countriesData'][$filterByCountryType];
    }

    protected function _getInvalidCountries()
    {
        $countriesAll = $this->getCountriesList(true);
        $countriesByType = $this->getCountriesList(false);
        return array_diff($countriesAll, $countriesByType);
    }

    /**
     * Prepare countries phone code for html options
     * @return array
     */
    protected function _preparePhoneCode()
    {
        $result = [];
        $countriesPhoneCode = $this->_getCountriesPhoneCode();
        foreach ($countriesPhoneCode as $countryCode => $phoneCode) {
            $result[$countryCode] = ['data-target' => '+' . $phoneCode];
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getRedirectUrlAfter()
    {
        return (new RedirectAfterActionInit(RedirectAfterRegistration::ID))->getUrl();
    }

    protected function _setCountryByIP()
    {
        $country = Interlayer_Crm::getCountryByIP(TSInit::$app->request->userIP);
        if (
            !empty($country['country_code'])
            && !array_key_exists($country['country_code'], $this->_getInvalidCountries())
        ) {
            $this->country = $country['country_code'];
        }
    }

    protected function _setPhoneCode()
    {
        $countriesPhoneCode = $this->_getCountriesPhoneCode();
        if (!empty($countriesPhoneCode[$this->country])) {
            $this->phoneCode = '+' . $countriesPhoneCode[$this->country];
        }
    }

    protected function _setCurrency()
    {
        if (!$this->withCurrency() || empty($this->country)) {
            return;
        }
        $currenciesData = $this->_getCurrencies();
        $this->currency = Arr::get($currenciesData, "countriesCurrency.{$this->country}");
    }

    /**
     * Get countries phone code
     * @return array
     */
    protected function _getCountriesPhoneCode()
    {
        if (!isset(self::$_cache['countriesPhoneCode'])) {
            $data = Interlayer_Crm::getCountriesWithPhoneCode();
            self::$_cache['countriesPhoneCode'] = empty($data['countries']['phoneCodes']) ? []
                : $data['countries']['phoneCodes'];
        }

        return self::$_cache['countriesPhoneCode'];
    }

    /**
     * Get currencies data
     * @return array
     */
    protected function _getCurrencies()
    {
        if (!isset(self::$_cache['currenciesData'])) {
            self::$_cache['currenciesData'] = Interlayer_Crm::getCurrencyList();
        }

        return self::$_cache['currenciesData'];
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
                $value = \TS_Functions::arrGet($_COOKIE, $cookie);
                if (!is_null($value)) {
                    $cookieParams[$paramName] = $value;
                    break;
                }
            }
        }

        return $cookieParams;
    }

    /**
     * Registration user
     * Request API to CRM
     * @return mixed
     */
    protected function _registration()
    {
        $requestParams = array_merge(
            [
                'formId'                 => $this->_formId,
                'firstName'              => $this->fname,
                'lastName'               => $this->lname,
                'country'                => $this->country,
                'phone'                  => $this->phoneCode . $this->phone,
                'email'                  => $this->email,
                'password'               => $this->password,
                'passwordConfirmation'   => $this->confirmPassword,
                'iAgree'                 => 1,
                'submit'                 => 'submit',
                'client_time'            => date('Y-m-d H:i:s'),
                'client_timezone_offset' => 0,
                'language'               => Multi_Language::getInstance()->getCurrentLanguage(),
                'url'                    => TSInit::$app->request->pathBase,
                'ip'                     => TSInit::$app->request->userIP,
            ],
            $this->_getCookieParams()
        );

        if ($this->promoCode) {
            $requestParams['promoCode'] = $this->promoCode;
        }

        if ($this->withCurrency() && $this->currency) {
            $requestParams['currency'] = $this->currency;
        }

        if ($this->withNotUSReportablePerson() && $this->notUSReportablePerson) {
            $requestParams['notUSReportablePerson'] = (int)$this->notUSReportablePerson;
        }

        if ($this->withReceiveEmailNewslettersAgreement()) {
            $requestParams['agreedReceiveNewsletters'] = (int) $this->agreedReceiveNewsletters;
        }

        if ($this->withPrivacyPolicy()) {
            $requestParams['agreedPrivacyPolicy'] = (int) $this->agreedPrivacyPolicy;
        }

        if ($this->withCreateExternalAccount()) {
            $requestParams['createExternalAccount'] = (int) $this->createExternalAccount;
        }

        return Interlayer_Crm::createAccount($requestParams);
    }

    protected function _auth()
    {
        return Interlayer_Crm::loginByUsername($this->email, addslashes($this->password));
    }

    protected function _beforeSave()
    {}

    /**
     * @param $url string
     * @return bool
     */
    protected function _isForceRedirect($url)
    {
        $urlInfo = parse_url($url);
        if ($urlInfo['host'] != TSInit::$app->request->hostName) {
            return true;
        }
        return false;
    }

    /**
     * Prepare error model from API response
     * @param $data mixed
     */
    abstract protected function _prepareError($data);

    protected function _getIntAttributes()
    {
        return [
            static::FIELD_ACCEPT_TC,
            static::FIELD_NOT_US_REPORTABLE_PERSON,
            static::FIELD_AGREED_PRIVACY_POLICY,
            static::FIELD_AGREED_RECEIVE_NEWS_LETTERS,
            static::FIELD_CREATE_EXTERNAL_ACCOUNT,
        ];
    }
}