<?php
/** @var \tradersoft\model\Registration|\tradersoft\model\Registration_Islamic $model */
$model = TSInit::$app->getVar('registrationModel');
/** @var \tradersoft\helpers\Session $session */
$session =TSInit::$app->session;
/** @var bool  $showAllCountries */
$showAllCountries = TSInit::$app->getVar('showAllCountries');
/** @var bool $allowPromoCode */
$allowPromoCode = TSInit::$app->getVar('allowPromoCode');
/** @var string  $enableCaptcha */
$enableCaptcha = TSInit::$app->getVar('enableCaptcha');
/** @var string $filterByCountryType */
$filterByCountryType = TSInit::$app->getVar('filterByCountryType');
?>

<div class="form-wrap">
    <?php require dirname(__FILE__) . '/registration-form.php'; ?>
</div>