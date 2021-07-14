<?php
/** @var $traderForgotPasswordModel \tradersoft\model\Trader_Login */
$modelTraderLogin = TSInit::$app->getVar('modelTraderLogin');
$response = TSInit::$app->getVar('response');
?>
<div class="form-wrap">
    <div class="login_form">
        <h3><?php echo \TS_Functions::__('Log in to your account') ?></h3>
        <?php if (!$response['success']): ?>
            <div class="error"><?php echo $response['message']; ?></div>
        <?php endif; ?>

        <?php $form = \tradersoft\helpers\Form::begin($modelTraderLogin)?>
        <?php echo \tradersoft\helpers\Html::hiddenInput('tradersoft_submit', 'trader_login'); ?>
        <?php echo $form->field('email'); ?>
        <?php echo $form->field('password')->passwordInput(); ?>
        <?php
        echo $form->field('rememberMe', [
                'options' => ['class' => 'form-row keep-logged-block'],
                'template' => "{input}\n{label}\n{error}\n{htmlBlock}",
            ])
            ->checkbox([
                    'boxOptions' => ['class' => 'keep-logged']
            ])
            ->htmlBlock('<a href="' . \tradersoft\helpers\Link::getTraderForgotLink() . '">' . \TS_Functions::__('Forgot password?') . '</a>');
        ?>
        <div class="form-row">
            <?php echo \tradersoft\helpers\Html::submitInput(\TS_Functions::__('Submit')); ?>
        </div>
        <?php \tradersoft\helpers\Form::end()?>
    </div>
</div>