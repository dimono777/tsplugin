<?php

use tradersoft\helpers\Interlayer_Crm;
use tradersoft\helpers\Page;

$current_password = $post['current_password'];
$new_password     = $post['new_password'];
$re_new_password  = $post['re_new_password'];

$data = false;
if ($current_password AND $new_password AND ($new_password == $re_new_password))
{
    $data = Interlayer_Crm::passwordHasBeenChanged(
        TSInit::$app->trader->get('crmId', 0),
        $new_password,
        $current_password,
        1
    );
}

if ($data)
{
    if (isset($data['returnCode']) AND $data['returnCode'] == Interlayer_crm::RESPONSE_CODE_SUCCESS)
    {
        $_POST['success']['change_password'] = \TS_Functions::__('The new password was sent to your email');
        $post_name = Page::getFieldValueByKey('[TS-AFTER-CHANGE-PASSWORD]', 'post_name');
        if ( ! is_null($post_name))
        {
            TS_Functions::redirectJS(TS_Functions::link($post_name));
        }
        else 
        {
            TS_Functions::redirectJS(TS_Functions::link('/'));
        }
    }
    else 
    {
        if (isset($data['description']))
        {
            $_POST['error']['change_password'] = \TS_Functions::__($data['description']);
        }
        $_POST['error']['returnCode'] = $data['returnCode'];
    }
    
}