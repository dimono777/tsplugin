<?php
namespace tradersoft\model;

use tradersoft\helpers\Interlayer_Partner;
use tradersoft\helpers\Session;

/**
 * Partners authorization model
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Partners_Authorization extends Model
{
    public $email;
    public $password;
    public $keep = 1;

    private $_globalErrors = [
        1 => 'Token is not active',
        2 => 'User is not found',
        100 => 'Unknown Error',
    ];

    public function rules()
    {
        return [

            ['email', 'stripTags'],
            [['email', 'password'], 'required'],
            ['email', 'minLength', 5],
            ['password', 'minLength', 3],
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
            'password' => \TS_Functions::__('Password'),
            'keep' => \TS_Functions::__('Keep me logged in'),
        ];
    }

    public function init()
    {
        if (isset($_GET['auth_error']) && array_key_exists($_GET['auth_error'], $this->_globalErrors)) {
            $session = new Session();
            $session->setFlash('error_partners_authorization', \TS_Functions::__($this->_globalErrors[$_GET['auth_error']]));
            unset($_GET['auth_error']);
        }
        parent::init();
    }

    public function auth()
    {
        $data = Interlayer_Partner::auth(['username'=>$this->email, 'password'=>md5($this->password)]);
        if (!$data) {
            return false;
        }
        $data = json_decode($data);
        $returnCode = isset($data->{'returnCode'}) ?
            $data->{'returnCode'}
            : Interlayer_Partner::RESPONSE_CODE_UNSPECIFIED_ERROR;

        $result['data'] = $data;
        $result['isAuth'] = $returnCode == Interlayer_Partner::RESPONSE_CODE_SUCCESS ? true : false;
        return $result;
    }
}