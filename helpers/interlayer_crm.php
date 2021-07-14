<?php

namespace tradersoft\helpers;

use tradersoft\cache\TransientCache;
use tradersoft\components\DataPolicyRegistration;
use tradersoft\helpers\multi_language\Multi_Language;
use TS_Functions;
use TSInit;

/**
 * Interlayer crm helper for crm project.
 */
class Interlayer_Crm extends Interlayer
{
    const RESPONSE_CODE_SUCCESS = 0;
    const RESPONSE_CODE_ACCOUNT_NOT_FOUND = 1;
    const RESPONSE_CODE_REQUIRED_FIELD_MISSING = 2;
    const RESPONSE_CODE_FIELD_NOT_VALID = 3;
    const RESPONSE_CODE_INVALID_DATA = 4;
    const RESPONSE_CODE_WRONG_PASSWORD = 5;
    const RESPONSE_CODE_WRONG_PASSWORD_WITH_NOTIFICATION = 6;
    const RESPONSE_CODE_LAST_COUNTRY_CHANGED = 7;
    const RESPONSE_CODE_TOKEN_INVALID = 8;
    const RESPONSE_CODE_TOKEN_EXPIRED = 9;
    const RESPONSE_CODE_WRONG_DOMAIN = 13;
    const RESPONSE_CODE_WRONG_OLD_PASSWORD = 15;
    const RESPONSE_CODE_PASSWORD_IN_BAN = 16;
    const RESPONSE_CODE_TOO_MANY_PASSWORD_RESETS = 34;
    const RESPONSE_CODE_WRONG_REGION = 44;
    const RESPONSE_CODE_MAX_QUIZ_SUBMIT_ATTEMPTS = 45;
    const RESPONSE_CODE_NO_ALLOWED_QUIZZES = 46;
    const RESPONSE_CODE_DOCUMENTS_UPLOAD_FORBIDDEN = 68;
    const RESPONSE_CODE_GBG_VERIFICATION_FAIL = 80;
    const RESPONSE_CODE_GBG_VERIFICATION_ATTEMPTS_EXCEEDED = 81;
    const RESPONSE_CODE_GBG_DISALLOWED_CURRENT_STATUS = 82;
    const RESPONSE_CODE_UNSPECIFIED_ERROR = 100;
    const RESPONSE_CODE_FORM_FIELD_NOT_VALID = 120;
    const RESPONSE_CODE_FORM_NOT_FOUND = 121;
    const RESPONSE_CODE_INVALID_SURVEY_TYPE = 220;
    const RESPONSE_CODE_STRICT_WRONG_REGION = 262;

    /**
     * @param $params array
     * @return mixed
     */
    public static function createAccount($params)
    {
        static::_formatLanguageParam($params, 'language');

        return self::sendRequest('/api/create-account/', $params);
    }

    /**
     * This functions accepts array which should contain following keys as api params:
     *  - traderID   (required)
     *  - email      (email, max_length(60))
     *  - firstName  (max_length(100))
     *  - lastName   (max_length(100))
     *  - address    (max_length(255))
     *  - town       (max_length(255))
     *  - postalCode (max_length(20))
     *  - country    (max_length(2))
     *  - phone      (max_length(255))
     *  - cellphone  (max_length(255))
     *  - birthday   (max_length(255))
     *  - username   (max_length(255))
     *  - ip         (ip)
     *
     * @param $params array
     *
     * @return mixed
     */
    public static function updateAccount($params)
    {
        return self::jsonDecode(self::sendRequest('/api/update-account/', $params));
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return mixed
     */
    public static function loginByUsername($username, $password) {
        $request = TSInit::$app->request;
        $url = ($request->isLocal) ? 'kievphp.com' : $request->getHostName();
        $url .=  TSInit::$app->request->getPath();

        $params = [
            'username'      => $username,
            'password'      => $password,
            'url'           => $url,
            'ip'            => $request->userIP,
            'notAuthedUID'  => TS_Functions::initNotAuthedUID(),
            'clienIdsArray' => []
        ];
        return self::sendRequest('/api/login-by-username/', $params);
    }

    /**
     * @param string $token
     *
     * @return mixed
     */
    public static function loginByToken($token)
    {
        $request = TSInit::$app->request;
        $url = $request->getHostName() .  $request->getPath();

        $params = [
            'ip'            => $request->userIP,
            'url'           => $url,
            'notAuthedUID'  => TS_Functions::initNotAuthedUID(),
            'token'         => $token,
            'clienIdsArray' => [3]
        ];
        return self::jsonDecode(
            self::sendRequest(
                '/api/login-by-token/', $params
            )
        );
    }

    /**
     * @param string $token
     *
     * @return mixed
     */
    public static function getLeadIdByPasswordRecoveryToken($token)
    {
        $request = TSInit::$app->request;
        $url = $request->getHostName() .  $request->getPath();
        return self::jsonDecode(
            self::sendRequest(
                '/api/get-lead-id-by-password-recovery-token/',
                [
                    'token' => $token,
                    'domain' => $url,
                ]
            )
        );
    }

    /**
     * @param $params array
     * @return mixed
     */
    public static function loginByCookies($params)
    {

        $params['ip'] = TSInit::$app->request->userIP;
        $params['url'] = TSInit::$app->request->hostName;
        $params['clienIdsArray'] = Config::get('trader.clientIdsArray', []);

        return self::sendRequest(
            '/api/login-by-cookies/',
            $params
        );
    }

    /**
     * @return mixed
     */
    public static function getCountriesWithPhoneCode()
    {
        return self::jsonDecode(self::sendRequest('/api/get-countries-with-phone-code/'));
    }

    /**
     * @param $lang null|string
     * @param $withInvalid int
     * @return mixed
     */
    public static function getCountriesAll($lang = null, $withInvalid = 0)
    {
        $withInvalid = (int) $withInvalid;
        $lang = static::getCRMLanguage($lang);

        static $countries = [];
        if (empty($countries[$lang][$withInvalid])) {
            $responseData = static::jsonDecode(
                static::sendRequest('/api/get-countries-all/', [
                    'lang' => $lang,
                    'withInvalid' => $withInvalid,
                ])
            );

            $countries[$lang][$withInvalid] = !empty($responseData['countries'])
                ? $responseData['countries']
                : [];
        }

        return $countries[$lang][$withInvalid];
    }

    /**
     * Return states list
     * @param $lang null|string
     * @return array
     */
    public static function getStates($lang = null)
    {
        $lang = static::getCRMLanguage($lang);
        $cacheKey = 'get-states:' . $lang;
        $response = TransientCache::get($cacheKey);
        if (empty($response)) {
            $response = self::jsonDecode(
                self::sendRequest('/api/get-states/', ['lang' => $lang])
            );
            if (isset($response['states'])) {
                TransientCache::set($cacheKey, $response);
            }
        }

        return empty($response['states']) ? [] : $response['states'];
    }

    /**
     * @param $lang null|string
     * @param $withInvalid bool
     * @param $getAllCountries bool
     * @param $filterByCountryType bool
     * @return mixed
     */
    public static function getCountriesByCountryTypes(
        $lang = null,
        $withInvalid = true,
        $getAllCountries = true,
        $filterByCountryType = false
    )
    {
        $requestParams = [
            'lang' => static::getCRMLanguage($lang),
            'withInvalid' => (int)$withInvalid,
            'getAllCountries' => (int)$getAllCountries,
            'countryType' => '',
        ];

        if ($filterByCountryType) {
            $requestParams['countryType'] = DataPolicyRegistration::getRegistrationFormCountryTypeSetting();
        }

        $countries = self::jsonDecode(
            self::sendRequest('/api/get-countries-by-country-types/', $requestParams)
        );

        return empty($countries['data']) ? [] : $countries['data'];
    }

    /**
     * Get lead info from interlayer
     * @author Bogdan Medvedev <bogdanmedvedev@tstechpro.com>
     * @param int $crmLeadId - crm lead id
     * @return mixed
     */
    public static function getLeadInfo($crmLeadId)
    {
        return self::jsonDecode(
            self::sendRequest('/api/get-lead-info/', ['leadId' => $crmLeadId])
        );
    }

    /**
     * get economic calendar data
     *
     * @author Roman Lazarskiy <roman.lazarskiy@tstechpro.com>
     *
     * @param string $language
     * @return array
     */
    public static function getEconomicCalendarData($language = null)
    {
        return self::jsonDecode(
            self::sendRequest('/api/get-economic-calendar-data/', [
                'language' => static::getCRMLanguage($language),
            ])
        );
    }

    /**
     * get economic calendar data
     *
     * @author Roman Lazarskiy <roman.lazarskiy@tstechpro.com>
     *
     * @param string $accountNumber
     * @return array
     */
    public static function getLeadByAccountNumber($accountNumber)
    {
        return self::jsonDecode(
            self::sendRequest('/api/get-lead-by-account-number/', ['accountNumber' => $accountNumber])
        );
    }

    /**
     * This functions accepts array which should contain following keys as api params:
     *  - fullname (max_length(255))
     *  - phone    (required, regex(/^\+?[\d\s\-]{7,20}$/))
     *  - country  (max_length(255))
     *  - email    (required, email, max_length(255));
     *  - language (length(2))
     *
     * @param array $params
     *
     * @return array
     */
    public static function callBackRequest(array $params)
    {
        static::_formatLanguageParam($params, 'language');

        return self::jsonDecode(
            self::sendRequest('/api/call-back-request/', $params)
        );
    }

    /**
     * @param string $leadID
     * @param string $newPassword
     * @param string $oldPassword
     * @param bool   $sendEmailToClient
     *
     * @return array
     */
    public static function passwordHasBeenChanged($leadID, $newPassword, $oldPassword, $sendEmailToClient) {
        return self::jsonDecode(
            self::sendRequest(
                '/api/password-has-been-changed/',
                [
                    'leadID'            => $leadID,
                    'newPassword'       => $newPassword,
                    'oldPassword'       => $oldPassword,
                    'sendEmailToClient' => $sendEmailToClient
                ]
            )
        );
    }

    /**
     * This functions accepts array which should contain following keys as api params:
     *  - email    (max_length(255))
     *  - language (length(2))
     *  - topic    (max_length(255))
     *  - fullName (max_length(255))
     *  - phone    (regex(/^\+?[\d\s\-]{7,20}$/))
     *  - message  (max_length(32768))
     * @param array $params
     *
     * @return array
     */
    public static function contactUsRequest(array $params)
    {
        return self::jsonDecode(
            self::sendRequest('/api/contact-us-request/', $params)
        );
    }

    /**
     * @param string $email
     *
     * @return array
     */
    public static function tryForgotPassword($email)
    {

        return self::jsonDecode(
            self::sendRequest(
                '/api/try-forgot-password/',
                [
                    'email' => $email,
                    'domain' => TSInit::$app->request->getMainDomain(),
                ]
            )
        );
    }

    /**
     * @return array
     */
    public static function getContactUsTopics()
    {
        return self::jsonDecode(
            self::sendRequest('/api/get-contact-us-topics/')
        );
    }

    /**
     * @param int  $amount
     * @param null $username
     *
     * @return mixed
     */
    public static function withdrawalRequest($username, $amount)
    {
        $params = [
            'username' => $username,
            'amount'   => $amount
        ];
        return self::jsonDecode(
            self::sendRequest('/api/withdrawal-request/', $params)
        );
    }

    /**
     * @param int $leadId
     * @param int $requestId
     *
     * @return mixed
     */
    public static function cancelWithdrawalRequest($leadId, $requestId)
    {
        $params = [
            'requestId' => $requestId,
            'leadId'    => $leadId
        ];
        return self::jsonDecode(
            self::sendRequest('/api/cancel-withdrawal-request/', $params)
        );
    }

    /**
     * @param int $leadID
     *
     * @return array
     */
    public static function getFeeIndexes($leadID)
    {
        $params = [
            'leadID' => $leadID,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        return self::jsonDecode(
            self::sendRequest('/api/get-fee-indexes/', $params)
        );
    }

    /**
     * @param $leadId
     *
     * @return array
     */
    public static function getWithdrawalRequestsByLeadId($leadId)
    {
        return self::jsonDecode(
            self::sendRequest('/api/get-withdrawal-requests-by-lead-id/', ['leadId' => $leadId])
        );
    }

    /**
     * @param $leadId
     * @param $file
     *
     * @param $fileName
     * @param $categoryId
     * @param $categoryTypeId
     * @param $comment
     * @param $sourceKey
     *
     * @return array
     * @internal param $params
     */
    public static function attachLeadFile($leadId, $file, $fileName, $categoryTypeId, $comment, $sourceKey)
    {
        $params = [
            'leadId' => $leadId,
            'sourceKey' => $sourceKey,
            'fileName' => $fileName,
            'categoryTypeId' => $categoryTypeId,
            'comment' => $comment,
        ];

        return self::jsonDecode(
            self::sendRequest('/api/attach-lead-file/', $params, ['file' => $file])
        );
    }

    /**
     * Returns upload file details
     *
     * @param int $leadId
     *
     * @return array
     * @internal param $params
     *
     * @author Igor Popravka <igor.popravka@tstechpro.com>
     */
    public static function getUploadFileDetails($leadId)
    {
        return self::jsonDecode(
            self::sendRequest('/api/get-upload-file-details/', [
                'leadId' => $leadId
            ])
        );
    }

    /**
     * get available questions for lead survey
     * by page
     *
     * @author Roman Lazarskiy <roman.lazarskiy@tstechpro.com>
     * @author anonymous
     *
     * @param int $leadId
     * @param int $pageId
     * @param null|string $language in iso format [2]
     * @return mixed
     */
    public static function getAvailableSurveysQuestionsForPage($leadId, $pageId, $language = null)
    {
        return self::jsonDecode(
            self::sendRequest('/api/get-available-surveys-questions-for-page/', [
                'language' => static::getCRMLanguage($language),
                'leadId' => $leadId,
                'pageId' => $pageId
            ])
        );
    }

    /**
     * get valid lead survey tree with answers
     *
     * @author Roman Lazarskiy <roman.lazarskiy@tstechpro.com>
     * @author anonymous
     *
     * @param array $newQuestionsAndAnswers
     * @param int $leadId
     * @param int $pageId
     * @param null|string $language in iso format [2]
     * @return mixed
     */
    public static function getValidSurveyTreeWithAnswers($newQuestionsAndAnswers, $leadId, $pageId, $language = null)
    {
        return self::jsonDecode(
            self::sendRequest('/api/get-valid-survey-tree-with-answers/', [
                'answers' => $newQuestionsAndAnswers,
                'leadId' => $leadId,
                'pageId' => $pageId,
                'language' => static::getCRMLanguage($language),
            ])
        );
    }

    /**
     * get survey form
     *
     * @author Roman Lazarskiy <roman.lazarskiy@tstechpro.com>
     * @author anonymous
     *
     * @param $leadId
     * @param $preResult
     * @param $mandatoryNotFilled
     * @param $pageId
     * @param null $language
     * @return mixed
     */
    public static function getSurveyForm($leadId, $preResult, $mandatoryNotFilled, $pageId, $language = null)
    {
        return self::jsonDecode(
            self::sendRequest('/api/get-survey-form/', [
                'preResult' => $preResult,
                'pageId' => $pageId,
                'mandatoryNotFilled' => $mandatoryNotFilled,
                'leadId' => $leadId,
                'language' => static::getCRMLanguage($language),
            ])
        );
    }

    /**
     * set survey form
     *
     * @author Roman Lazarskiy <roman.lazarskiy@tstechpro.com>
     * @author anonymous
     *
     * @param int $leadId
     * @param array $answers
     * @return mixed
     */
    public static function setSurveyForm($leadId, array $answers) {
        return self::jsonDecode(
            self::sendRequest('/api/set-survey-form/', [
                'leadId' => $leadId,
                'answers' => $answers
            ])
        );
    }

    /**
     * get survey pages count
     *
     * @author Roman Lazarskiy <roman.lazarskiy@tstechpro.com>
     * @author anonymous
     *
     * @param int $leadId
     * @return mixed
     */
    public static function getSurveyPagesCount($leadId)
    {
        return self::jsonDecode(
            self::sendRequest('/api/get-survey-pages-count/', ['leadId' => $leadId])
        );
    }

    /**
     * Getting survey initia; data
     *
     * @param int $leadId
     *
     * @return mixed
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public static function getSurveyInitialData($leadId)
    {
        return self::jsonDecode(
            self::sendRequest('/api/get-survey-initial-data/', [
                'leadId' => $leadId,
                'language' => static::getCurrentLanguage(),
            ])
        );
    }

    /**
     * get questions count by page
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param int $leadId
     * @return mixed
     */
    public static function getQuestionsCountByPage($leadId)
    {
        return self::jsonDecode(
            self::sendRequest('/api/get-survey-questions-count-by-page/', ['leadId' => $leadId])
        );
    }

    /**
     * Getting lead suitability application data
     *
     * @param int $leadId
     *
     * @return mixed
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public static function getLeadSuitabilityApplicationData($leadId)
    {
        return self::jsonDecode(
            self::sendRequest('/api/get-lead-suitability-application-data/', ['leadId' => $leadId])
        );
    }

    /**
     * Sending lead professional request
     *
     * @param int $leadId
     * @param array $answeredQuestions
     *
     * @return mixed
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public static function sendLeadProfessionalRequest($leadId, array $answeredQuestions)
    {
        return self::jsonDecode(
            self::sendRequest('/api/send-lead-professional-request/', [
                'leadId' => $leadId,
                'answeredQuestions' => $answeredQuestions,
            ])
        );
    }

    /**
     * check survey mandatory fields
     *
     * @author Roman Lazarskiy <roman.lazarskiy@tstechpro.com>
     * @author anonymous
     *
     * @param array $answers
     * @param int $pageId
     * @param int $leadId
     * @param null|string $language in iso format [2]
     * @return mixed
     */
    public static function checkSurveyMandatoryFields(array $answers, $pageId, $leadId, $language = null) {
        return self::jsonDecode(
            self::sendRequest(
                '/api/check-survey-mandatory-fields/',
                [
                    'answers' => $answers,
                    'pageId' => $pageId,
                    'leadId' => $leadId,
                    'language' => static::getCRMLanguage($language),
                ]
            )
        );
    }

    /**
     * get survey info for lead
     *
     * @author Roman Lazarskiy <roman.lazarskiy@tstechpro.com>
     * @author anonymous
     *
     * @param int $pageId
     * @param int $leadId
     * @param null|string $language in iso format [2]
     * @return mixed
     */
    public static function getSurveyInfo($pageId, $leadId, $language = null)
    {
        return self::jsonDecode(
            self::sendRequest(
                '/api/get-survey-info/',
                [
                    'pageId' => $pageId,
                    'leadId' => $leadId,
                    'language' => static::getCRMLanguage($language),
                ]
            )
        );
    }

    /**
     * Returns deposit link
     *
     * @author Denis Chunyak <denis.chunyak@tstechpro.com>
     *
     * @param $crmLeadId
     * @return mixed
     */
    public static function getDepositLink($crmLeadId)
    {
        return self::jsonDecode(
            self::sendRequest('/api/get-deposit-link/', ['leadId' => $crmLeadId])
        );
    }

    /**
     * Return country by IP
     *
     * @param $ip string
     * @return array
     */
    public static function getCountryByIP($ip)
    {
        static $countryByIp = [];
        if (empty($countryByIp[$ip])) {
            $countryByIp[$ip] = static::jsonDecode(
                static::sendRequest('/api/get-client-country/', ['ip' => $ip])
            );
        }

        return !empty($countryByIp[$ip]['clientCountry'])
            ? $countryByIp[$ip]['clientCountry']
            : [];
    }

    /**
     * Return phone by IP
     *
     * @return mixed
     */
    public static function getPhoneCodeByIP()
    {
        static $phoneCodesByIP = [];

        $ip = TSInit::$app->request->userIP;
        $phoneCodeByIP = Arr::get($phoneCodesByIP, $ip);
        if (empty($phoneCodeByIP)) {
            $response = self::jsonDecode(self::sendRequest('/api/get-phone-code-by-ip/', ['ip' => $ip]));
            $phoneCodeByIP = Arr::get($response, 'phoneCode');
            if(!empty($phoneCodeByIP)) {
                $phoneCodesByIP[$ip] = $phoneCodeByIP;
            }
        }

        return $phoneCodeByIP ? : '';
    }

    /**
     * Get webinars for lead show by date range and language
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param int $startDate timestamp
     * @param int $endDate timestamp
     * @param string $language [2]
     * @param int $traderId
     *
     * @return array
     */
    public static function getWebinarsForTraderByDateRange($startDate, $endDate, $language, $traderId = 0)
    {
        /** @var array */
        $params = [
            'startTimestamp' => $startDate,
            'endTimestamp' => $endDate,
            'language' => static::getCRMLanguage($language),
        ];

        if ($traderId) {
            $params['leadId'] = $traderId;
        }

        $response = self::jsonDecode(
            self::sendRequest(
                '/api/get-webinars-by-date-interval/',
                $params
            )
        );

        return (is_array($response)) ? $response : [];
    }

    /**
     * Join lead to webinar
     *
     * @author Roman Lazarskiy <roman.lazarskiy@tstechpro.com>
     *
     * @param int $leadId
     * @param int $webinarId
     * @return array
     */
    public static function joinLeadToWebinar($leadId, $webinarId)
    {
        $response = self::jsonDecode(
            self::sendRequest(
                '/api/join-lead-to-webinar/',
                ['leadId' => $leadId, 'webinarId' => $webinarId]
            )
        );

        /** Return an empty array of join data on failed result of request */
        if (
            !is_array($response)
            || (
                array_key_exists('statusCode', $response)
                && $response['statusCode'] === null
            )
        ) {
            return [];
        }

        return $response;
    }

    /**
     * Function quizPassed
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $leadId
     * @param mixed $results
     *
     * @return array|mixed
     */
    public static function quizPassed($leadId, $results)
    {
        $response = self::jsonDecode(
            self::sendRequest(
                '/api/quiz-passed/',
                ['leadId' => $leadId, 'results' => $results]
            )
        );

        return (is_array($response)) ? $response : [];
    }

    /**
     * Function quizAttemptsLimitExceeded
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $leadId
     *
     * @return array
     */
    public static function quizAttemptsLimitExceeded($leadId)
    {
        $response = self::jsonDecode(
            self::sendRequest(
                '/api/quiz-attempts-limit-exceeded/',
                ['leadId' => $leadId]
            )
        );

        return (is_array($response)) ? $response : [];
    }

    /**
     * Get currency list
     * [
     *       'countriesCurrency' => [
     *           'countryCode' => 'currencyCode',
     *       ],
     *       'allCurrencies' => [
     *          'code' => [
     *              'code' => code,
     *              'title' => title,
     *              'symbol' => symbol,
     *              'precision' => code,
     *          ],
     *       ]
     * ]
     *
     * @return array
     */
    public static function getCurrencyList()
    {
        $response = self::jsonDecode(
            self::sendRequest('/api/get-currency-list/')
        );

        return (is_array($response) && isset($response['currencies'])) ? $response['currencies'] : [];
    }

    /**
     * Get quiz full data
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param int $leadId
     * @param string|null $language
     *
     * @return array
     */
    public static function getQuizFullData($leadId, $language = null)
    {
        $response = self::jsonDecode(
            self::sendRequest(
                '/api/get-quiz-full-data/',
                [
                    'leadId' => $leadId,
                    'language' => static::getCRMLanguage($language),
                ]
            )
        );

        return (is_array($response)) ? $response : [];
    }

    /**
     * Send quiz results
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param int $leadId
     * @param array $submissionResult
     *
     * @return array
     */
    public static function sendQuizResults($leadId, array $submissionResult)
    {
        $response = self::jsonDecode(
            self::sendRequest(
                '/api/send-quiz-results/',
                [
                    'leadId' => $leadId,
                    'submissionResult' => $submissionResult
                ]
            )
        );

        return (is_array($response)) ? $response : [];
    }

    /**
     * It checks appropriating of domain/ip by the regulation settings
     * returns valid domain for ip  (by country)
     *
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $ip
     *
     * @param $domain
     *
     * @return array|mixed
     */
    public static function checkDomainByIp($ip, $domain)
    {
        $response = self::jsonDecode(self::sendRequest('/api/check-domain-by-ip/', ['ip' => $ip, 'domain' => $domain]));

        return (is_array($response)) ? $response : [];
    }

    /**
     * @param int $leadId
     * @return array
     */
    public static function getAMLVerificationFormData($leadId)
    {
        $response = self::jsonDecode(self::sendRequest('/api/get-a-m-l-verification-form-data/', [
            'leadId' => $leadId,
            'lang' => static::getCurrentLanguage(),
        ]));

        return (is_array($response)) ? $response : [];
    }

    /**
     * @param int $leadId
     * @param array $data
     * @return array
     */
    public static function saveAMLVerificationFormData($leadId, $data)
    {
        $response = self::jsonDecode(self::sendRequest('/api/save-a-m-l-verification-form-data/', [
            'leadId' => $leadId,
            'data' => $data,
        ]));

        return (is_array($response)) ? $response : [];
    }

    /**
     * Getting form validation rules
     *
     * @return array|mixed
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public static function getFormValidationRules()
    {
        $response = static::jsonDecode(static::sendRequest('/api/get-form-validation-rules'));

        return is_array($response) ? $response : [];
    }

    /**
     * Returns correct language for CRM using configuration map file
     *
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     *
     * @param string $lang
     * @return string
     */
    public static function getCRMLanguage($lang)
    {
        if (empty($lang)) {
            $lang = Multi_Language::getInstance()->getCurrentLanguage();
        }

        return Arr::get(Config::get('crm_correct_language', []), $lang, $lang);
    }

    /**
     * @return string
     */
    public static function getCurrentLanguage()
    {
        return static::getCRMLanguage(Multi_Language::getInstance()->getCurrentLanguage());
    }

    public static function getTinValidationConfigData()
    {
        $response = (array)static::jsonDecode(static::sendRequest('/api/get-tin-validation-config-data'));

        return empty($response['data']) ? [] : $response['data'];
    }

    /**
     * @return array|mixed
     */
    public static function getVerificationFileCategoriesTypesList()
    {
        $response = static::jsonDecode(static::sendRequest('/api/get-verification-file-categories-types-list'));

        return $response['data'] ?: [];
    }

    /**
     * @param array $params
     * @param string $languageKey
     */
    protected static function _formatLanguageParam(array &$params, $languageKey)
    {
        $params[$languageKey] = static::getCRMLanguage(Arr::get($params, $languageKey));
    }
}