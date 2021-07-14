<?php
//TODO : Delete this file

use tradersoft\helpers\Interlayer_Crm;
use tradersoft\model\redirect_after_action\Init as RedirectAfterActionInit;
use tradersoft\model\redirect_after_action\actions\Authorization as RedirectAfterAuthorization;

$email      = $post['email'];
$password   = $post['password'];
$is_ajax    = isset($post['ajax']) ? $post['ajax'] : false;

$data = false;
if ($email AND $password)
{
    $data = tradersoft\helpers\Interlayer_Crm::loginByUsername($email, $password);
}

if ($data)
{
    $data = json_decode($data, true);
    if (isset($data['returnCode']) AND $data['returnCode'] == Interlayer_Crm::RESPONSE_CODE_SUCCESS AND isset($data['leadInfo']))
    {
        if (isset($post['keep']))
        {
            $keep = true;
        }
        else 
        {
            $keep = false;
        }

        $res = TSInit::$app->trader->login($data['leadInfo'], $keep);

        /** @var string $redirectUrl */
        $redirectUrl = (new RedirectAfterActionInit(RedirectAfterAuthorization::ID))->getUrl();

        if ($is_ajax && $res)
        {
            die(json_encode([
                'code' => 1,
                'redirectUrl' => $redirectUrl
            ]));
        }
        else 
        {
            TS_Functions::redirectJS($redirectUrl);
        }
    }
    else 
    {
        //RESPONSE_CODE_WRONG_REGION
        if ($data['returnCode'] == Interlayer_Crm::RESPONSE_CODE_WRONG_REGION) {
            if ($is_ajax) {
                die(
                    json_encode([
                        'code' => 1,
                        'redirectUrl' => '/'
                    ])
                );
            } else {
                TS_Functions::redirectJS(
                    \TSInit::$app->request->getLink('/')
                );
            }
        }

        //RESPONSE_CODE_WRONG_DOMAIN
        if (
            $data['returnCode'] == Interlayer_Crm::RESPONSE_CODE_WRONG_DOMAIN
            && isset($data['leadInfo']['redirectTo'])
        ) {
            if ($is_ajax) {
                die(
                    json_encode([
                        'code' => 1,
                        'redirectUrl' => $data['leadInfo']['redirectTo']
                    ])
                );
            } else {
                TS_Functions::redirectJS($data['leadInfo']['redirectTo']);
            }
        }

        if (isset($data['description']))
        {
            $_POST['error']['authorization'] = \TS_Functions::__($data['description']);
            if ($is_ajax)
            {
                die(json_encode(array('code' => 0, 'reason' => $_POST['error']['authorization'])));
            }
        }
        else 
        {
            if ($is_ajax)
            {
                die(json_encode(array('code' => 0, 'reason' => 'Unknown error')));
            }
        }
    }
}
else
{
    if ($is_ajax)
    {
        die(json_encode(array('code' => 0, 'reason' => 'There is an error in request')));
    }
}
