
<?php
/** @var $model \tradersoft\model\Registration_Demo */
$model = TSInit::$app->getVar('registrationModel');
/** @var $session \tradersoft\helpers\Session */
$session =TSInit::$app->session;
/** @var $enableCaptcha string */
$enableCaptcha = TSInit::$app->getVar('enableCaptcha');
?>

<div class="form-wrap">
    <?php require dirname(__FILE__) . '/registration-form.php'; ?>
</div>