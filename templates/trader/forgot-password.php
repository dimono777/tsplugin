<?php
/** @var $traderForgotPasswordModel \tradersoft\model\Trader_Forgot_Password */
$traderForgotPasswordModel = TSInit::$app->getVar('traderForgotPasswordModel');
/** @var string  $captchaType */
$captchaType = TSInit::$app->getVar('captchaType');
?>

<div class="form-wrap">
    <div class="forgot_password_form">
        <h3><?php echo \TS_Functions::__('Reset your password') ?></h3>
        <span class="desription"><?php echo \TS_Functions::__('Enter the email address you’ve registered with') ?></span>
        <?php /** @var $form \tradersoft\helpers\Form */?>
        <?php $form = \tradersoft\helpers\Form::begin($traderForgotPasswordModel, ['htmlOptions' => ['id'=>'reg_form']])?>
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
        <?php echo \tradersoft\helpers\Html::hiddenInput('tradersoft_submit', 'trader_forgot_password'); ?>
        <?php \tradersoft\helpers\Form::end()?>
    </div>
</div>