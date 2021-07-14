<?php

namespace tradersoft\model;

use tradersoft\helpers\Arr;
use tradersoft\helpers\captcha\Invisible_ReCaptcha;
use TSInit;

/**
 * Class ModelWithCaptcha
 *
 * @author Andrey Fomov
 *
 * @package tradersoft\model
 */
class ModelWithCaptcha extends Model
{
    /** @var string */
    public $captcha;

    /** @var array  */
    public $params = [];

    /**
     * ModelWithCaptcha constructor.
     *
     * @author Andrey Fomov
     *
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        parent::__construct();
        $this->params = $params;
    }

    public function afterLoad()
    {

        if (!$this->_checkCaptcha()) {
            $this->addError(
                'captcha',
                \TS_Functions::__('Suspicious activity has been detected. Please try again or contact support.')
            );
        }
    }

    /**
     * @author Andrey Fomov
     *
     *
     * @return bool
     *
     */
    protected function _checkCaptcha()
    {

        if (
            !isset($this->params['enableCaptcha'])
            || !Invisible_ReCaptcha::isEnabled()
        ) {
            return true;
        }
        if (!Arr::get($_POST, 'g-recaptcha-response')) {
            return false;
        } else {
            $this->captcha = Arr::get($_POST, 'g-recaptcha-response');

            return Invisible_ReCaptcha::verifyResponse(TSInit::$app->request->userIP, $this->captcha);
        }
    }
}