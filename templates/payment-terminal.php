<?php
    $response = tradersoft\helpers\Interlayer_Crm::getDepositLink(
        TSInit::$app->trader->get('crmId')
    );
    $iFrameSource = TS_Functions::arrGet($response, 'link');
?>

<iframe src="<?php echo $iFrameSource ?>"></iframe>