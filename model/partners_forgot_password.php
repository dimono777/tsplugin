<?php
namespace tradersoft\model;

use tradersoft\helpers\Interlayer_Partner;
use tradersoft\helpers\Session;

/**
 * Partners authorization model
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Partners_Forgot_Password extends ModelWithCaptcha
{
    public $email;

    public function rules()
    {
        return [

            ['email', 'stripTags'],
            ['email', 'required'],
            ['email', 'minLength', 5],
            ['email','email'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'email' => \TS_Functions::__('Email address'),
        ];
    }

    public function send()
    {
        $data = Interlayer_Partner::forgotPassword(['email'=>$this->email]);
        if (!$data) {
            return false;
        }

        $data = json_decode($data);
        $returnCode = isset($data->{'returnCode'}) ?
            $data->{'returnCode'}
            : Interlayer_Partner::RESPONSE_CODE_UNSPECIFIED_ERROR;

        $result['data'] = $data;
        $result['isSend'] = $returnCode == Interlayer_Partner::RESPONSE_CODE_SUCCESS ? true : false;

        return $result;
    }
}