<?php
/** @var $partnersForgotPasswordModel \tradersoft\model\Partners_Forgot_Password */
$partnersForgotPasswordModel = TSInit::$app->getVar('partnersForgotPasswordModel');
/** @var $session \tradersoft\helpers\Session */
$session =TSInit::$app->session;
/** @var string  $captchaType */
$captchaType = TSInit::$app->getVar('captchaType');
?>

<?php
if ($session->hasFlash('after_forgot_password')) {
    require_once dirname(__FILE__) . '/after-forgot-password.php';
} else {
?>

<div class="form-wrap">
    <div class="forgot_password_form">
        <h3><?php echo \TS_Functions::__('Reset your password') ?></h3>
        <span class="desription"><?php echo \TS_Functions::__('Enter the email address you’ve registered with') ?></span>
        <?php if ($session->hasFlash('error_partners_forgot_password')): ?>
            <div class="error"><?php echo $session->getFlash('error_partners_forgot_password'); ?></div>
        <?php endif; ?>

        <?php /** @var $form \tradersoft\helpers\Form */?>
        <?php $form = \tradersoft\helpers\Form::begin($partnersForgotPasswordModel, ['htmlOptions' => ['id'=>'reg_form']])?>
            <?php echo \tradersoft\helpers\Html::hiddenInput('tradersoft_submit', 'partner_forgot_password'); ?>
            <?php echo $form->field('email'); ?>
        <?php if ($captchaType) { ?>
            <?php echo $form->field('captcha')->captcha($captchaType) ?>
        <?php } ?>
            <div class="form-row">
                <?php
                    echo \tradersoft\helpers\Html::submitInput(
                        \TS_Functions::__('Submit'),
                        ($captchaType)
                            ? ['id' => 'regFormSubmit']
                            : []
                    );
                ?>
            </div>
        <?php \tradersoft\helpers\Form::end()?>

    </div>
</div>
<?php } ?>