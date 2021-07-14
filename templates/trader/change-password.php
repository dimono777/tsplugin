<?php
/** @var $traderChangePasswordModel \tradersoft\model\Trader_Change_Password */
$traderChangePasswordModel = TSInit::$app->getVar('traderChangePasswordModel');
?>

<div class="form-wrap">
    <div class="change_password">
        <h3><?php echo \TS_Functions::__('Change your password') ?></h3>
        <?php /** @var $form \tradersoft\helpers\Form */?>
        <?php $form = \tradersoft\helpers\Form::begin($traderChangePasswordModel)?>
        <?php echo $form->field('currentPassword')->passwordInput();?>
        <?php echo $form
            ->field('password',
                [
                    'template' => "{label}\n{input}\n{error}\n{htmlBlock}",
                ]
            )
            ->passwordInput()
            ->htmlBlock(
                '<div class="password-rules">
                    <ul>
                        <li>' . \TS_Functions::__('Use 5-15 characters') . '</li>
                        <li>' . \TS_Functions::__('You can use the next symbols:') . ' 
                            <span>! " # $ % & &#39; ( ) * + , - . / : ; < = > ? @ [ \ ] ^ _ ` { | } ~</span></li>
                        <li>' . \TS_Functions::__('Combine letters (capital and lower case)') . '
                            ' .\TS_Functions::__('with numbers') . '</li>
                        <li>' . \TS_Functions::__('Avoid common passwords like 1234, "password", or your name') . '</li>
                        <li>' . \TS_Functions::__('Don’t use gap in password') . '</li>
                    </ul>
                </div>'
            ); ?>
        <?php echo $form->field('confirmPassword')->passwordInput(); ?>
        <div class="form-row">
            <?php echo \tradersoft\helpers\Html::submitInput(\TS_Functions::__('Change password')); ?>
        </div>
        <?php echo \tradersoft\helpers\Html::hiddenInput('tradersoft_submit', 'trader_change_password'); ?>
        <?php \tradersoft\helpers\Form::end()?>
    </div>
</div>