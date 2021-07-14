<?php
namespace tradersoft\model;

use tradersoft\components\GoogleAnalytics;
use tradersoft\helpers\Arr;
use tradersoft\helpers\Interlayer_Crm;
use tradersoft\model\redirect_after_action\Init as RedirectAfterActionInit;
use tradersoft\model\redirect_after_action\actions\Authorization as RedirectAfterAuthorization;
use tradersoft\helpers\Trader_Auth_Cookie as TraderAuthCookie;

/**
 * Trader authorization model
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Trader_Login extends Model
{
    public $email;
    public $password;
    public $rememberMe = TraderAuthCookie::DEFAULT_REMEMBER_ME_VALUE;

    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'stripTags'],
            ['email', 'minLength', 5],
            ['email', 'maxLength', 254],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'email' => \TS_Functions::__('Email address'),
            'password' => \TS_Functions::__('Password'),
            'rememberMe' => \TS_Functions::__('Keep me logged in'),
        ];
    }

    /**
     * @return array
     */
    public function auth()
    {
        $result = ['isOk' => false];
        $data = Interlayer_Crm::loginByUsername($this->email,$this->password);

        if ($data) {
            $data = json_decode($data, true);
            $code = Arr::get($data, 'returnCode', Interlayer_Crm::RESPONSE_CODE_UNSPECIFIED_ERROR);
            $description = $this->getErrorMessageByErrorCode($code, Arr::get($data, 'description', 'Unknown error'));
            if ($code == Interlayer_Crm::RESPONSE_CODE_SUCCESS AND isset($data['leadInfo'])) {

                if (\TSInit::$app->trader->login($data['leadInfo'], (bool)$this->rememberMe)) {
                    return [
                        'isOk' => true,
                        'redirectUrl' => (new RedirectAfterActionInit(RedirectAfterAuthorization::ID))->getUrl(),
                    ];
                } else {
                    $result['message'] = \TS_Functions::__('Unknown error');
                }
            } else {
                //RESPONSE_CODE_WRONG_REGION
                if ($code == Interlayer_Crm::RESPONSE_CODE_WRONG_REGION) {
                    $result['redirectUrl'] = '/';
                }
                //RESPONSE_CODE_WRONG_DOMAIN
                if (
                    $code == Interlayer_Crm::RESPONSE_CODE_WRONG_DOMAIN
                    && isset($data['leadInfo']['redirectTo'])
                ) {
                    $result['redirectUrl'] = GoogleAnalytics::addClientIdToUrl(
                        $data['leadInfo']['redirectTo']
                    );
                }

                $result['message'] = \TS_Functions::__($description);
            }
        } else {
            $result['message'] = \TS_Functions::__('There is an error in request');
        }

        return $result;
    }

    public function getErrorMessageByErrorCode($errorCode, $msg = 'Unknown error')
    {
        switch ($errorCode) {
            case Interlayer_Crm::RESPONSE_CODE_ACCOUNT_NOT_FOUND:
            case Interlayer_Crm::RESPONSE_CODE_WRONG_PASSWORD:
            case Interlayer_Crm::RESPONSE_CODE_STRICT_WRONG_REGION:
            case Interlayer_Crm::RESPONSE_CODE_WRONG_PASSWORD_WITH_NOTIFICATION:
                $errorMessage = \TS_Functions::__('Wrong password or Email doesn’t exist');
                break;
            case Interlayer_Crm::RESPONSE_CODE_LAST_COUNTRY_CHANGED:
                $errorMessage = \TS_Functions::__('Due to a new country by IP detected, your password has been reset. A new password has been sent to you via e-mail. Please check your e-mail account.');
                break;
            case Interlayer_Crm::RESPONSE_CODE_TOKEN_INVALID:
                $errorMessage = \TS_Functions::__('We’re sorry, but your link is broken. Please send request to support.');
                break;
            case Interlayer_Crm::RESPONSE_CODE_TOKEN_EXPIRED:
                $errorMessage = \TS_Functions::__('We’re sorry, but your link was expired. Check your email for the new one.');
                break;
            default:
                $errorMessage = \TS_Functions::__($msg);
                break;
        }

        return $errorMessage;
    }
}