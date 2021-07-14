<?php
/** @var $partnersAuthorizationModel \tradersoft\model\Partners_Authorization */
$partnersAuthorizationModel = TSInit::$app->getVar('partnersAuthorizationModel');
/** @var $session \tradersoft\helpers\Session */
$session =TSInit::$app->session;
?>

<div class="form-wrap">
    <div class="login_form">
        <h3><?php echo \TS_Functions::__('Log in to your account') ?></h3>
        <?php if ($session->hasFlash('error_partners_authorization')): ?>
            <div class="error" style="padding-bottom: 10px;">
                <?php echo $session->getFlash('error_partners_authorization'); ?>
            </div>
        <?php endif; ?>

        <?php /** @var $form \tradersoft\helpers\Form */?>
        <?php $form = \tradersoft\helpers\Form::begin($partnersAuthorizationModel)?>
            <?php echo \tradersoft\helpers\Html::hiddenInput('tradersoft_submit', 'partner-authorization'); ?>
            <?php echo $form->field('email'); ?>
            <?php echo $form->field('password')->passwordInput(); ?>
            <?php
            echo $form->field(
                    'keep',
                    [
                        'template' => "{label}\n{input}".'<a href="'.\tradersoft\helpers\Partner::getPartnerForgotLink().'">'.\TS_Functions::__('Forgot password?').'</a>'."\n{error}",
                        'options' => ['class' => 'form-row keep-logged-block']
                    ]
                )->checkbox([
                    'boxOptions' => ['class' => 'keep-logged']
                ]);
            ?>
            <div class="form-row">
                <?php echo \tradersoft\helpers\Html::submitInput(\TS_Functions::__('Log in')); ?>
                <a href="<?php echo \tradersoft\helpers\Partner::getPartnerRegLink(); ?>" target="_blank" class="btn-partner-registration"><?php echo \TS_Functions::__('Registration');?></a>
            </div>
        <?php \tradersoft\helpers\Form::end()?>

    </div>
</div>