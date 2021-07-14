<?php
namespace tradersoft\model;

use tradersoft\helpers\ExternalFormValidationRule;
use tradersoft\helpers\Interlayer_Crm;
use tradersoft\helpers\Arr;
use TSInit;

/**
 * Trader forgot password model
 * @author Bogdan Medvedev <bogdan.medvedev@tstechpro.com>
 */
class Trader_Change_Password extends Model
{

    public $currentPassword;
    public $password;
    public $confirmPassword;

    /**
     * @return array
     */
    public function rules()
    {
        $rules = [
            [['password', 'confirmPassword', 'currentPassword'], 'required'],
            ['password', 'compare', [
                'skipOnEmpty' => false,
                'operator' => '==',
                'compareAttribute' => 'confirmPassword',
            ]],
            ['currentPassword', 'compare', [
                'skipOnEmpty' => false,
                'operator' => '!=',
                'compareAttribute' => 'password',
            ]],
        ];

        if ($minLengthPassword = ExternalFormValidationRule::getFieldRuleParams('password', 'minLength')) {
            $rules[] = ['password', 'minLength', $minLengthPassword];
        }

        if ($maxLengthPassword = ExternalFormValidationRule::getFieldRuleParams('password', 'maxLength')) {
            $rules[] = ['password', 'maxLength', $maxLengthPassword];
        }

        return $rules;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'currentPassword' => \TS_Functions::__('Current password'),
            'password' => \TS_Functions::__('New password'),
            'confirmPassword' => \TS_Functions::__('Re-enter new password'),
        ];
    }

    /**
     * Save account details
     * @return bool
     */
    public function send()
    {
        $data = Interlayer_Crm::passwordHasBeenChanged(
            TSInit::$app->trader->get('crmId', 0),
            $this->password,
            $this->currentPassword,
            true
        );

        $result = false;

        if ($data) {
            $returnCode = Arr::get($data, 'returnCode', Interlayer_Crm::RESPONSE_CODE_UNSPECIFIED_ERROR);

            if ($returnCode == Interlayer_Crm::RESPONSE_CODE_SUCCESS) {
                $result = true;
            } else {
                $this->_prepareError($data);
            }
        }

        return $result;
    }

    /**
     * Prepare error model from API response
     * @param $data mixed
     */
    protected function _prepareError($data)
    {
        switch($data['returnCode']) {
            case Interlayer_Crm::RESPONSE_CODE_WRONG_OLD_PASSWORD :
                $this->addError('currentPassword', \TS_Functions::__($data['description']));
                break;

            case Interlayer_Crm::RESPONSE_CODE_PASSWORD_IN_BAN :
            case Interlayer_Crm::RESPONSE_CODE_TOO_MANY_PASSWORD_RESETS :
                $this->addError('password', \TS_Functions::__($data['description']));
                break;

            case Interlayer_Crm::RESPONSE_CODE_FIELD_NOT_VALID :

                if (Arr::path($data,'validationErrors.oldPassword', false)) {

                    $invalidField = 'currentPassword';

                } elseif (Arr::path($data,'validationErrors.newPassword', false)) {

                    $invalidField = 'password';

                } else {

                    // as default we are showing errors on first input currentPassword.
                    $invalidField = 'currentPassword';

                }
                $this->addError($invalidField, \TS_Functions::__($data['description']));
                break;

            default :
                $this->addError('currentPassword', \TS_Functions::__($data['description']));
        }
    }
}