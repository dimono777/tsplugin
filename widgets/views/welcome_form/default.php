<?php
/**
 * Default template for ts_hello widget login form
 *
 * @var $form \tradersoft\helpers\Form
 * @var $modelTraderLogin \tradersoft\model\Base_Registration
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
?>
<div id="log-in" class="ts-log-in-wrapper modal fade" role="dialog">

    <div class="ts-log-in-block">    
        <h3 class="ts-log-in-title"><?php echo \TS_Functions::__('Login to your account'); ?></h3>
        <div id="auth_error_msg" style="color: red; font-size: 12px; padding-bottom: 5px;"></div>
        <div id="auth_success_msg" style="color: #d8a406; font-size: 12px; padding-bottom: 5px;"></div>
        
        <?php $modelTraderLogin = TSInit::$app->getVar('modelTraderLogin'); ?>
        <?php $form = \tradersoft\helpers\Form::begin(
            $modelTraderLogin,
            [
                'action' => '/trader/login',
                'htmlOptions' => ['id'=>'ts-hello-auth-form',],
                'ajaxEnable' => '1',
                'ajaxUrl' => '/trader/login',
                'ajaxCallback' => 'callBackLogin'
                ]
                )?>
            <?php echo \tradersoft\helpers\Html::hiddenInput('tradersoft_submit', 'trader_login'); ?>

        <?php echo $form->field('email', [])->textInput(['id'=>'auth_email','class'=>'form-input form-free',]);?>
        <?php echo $form->field('password', [])->passwordInput(['id'=>'auth_password','class'=>'form-input form-free',]);?>

        <div>
            <?php echo do_shortcode( '[get_link_by_short_code code="TS-FORGOT-PASSWORD" text="' . \TS_Functions::__('Forgot password?') . '"]');?>
        </div>
        <div>
            <button id="auth_submit" class="btn-default"><?php echo \TS_Functions::__('Log In'); ?></button>
        </div>
        <div>
            <?php echo do_shortcode( '[get_link_by_short_code code="TS-REGISTRATION" text="' . \TS_Functions::__('Sign up') . '"]');?>
        </div>
        <?php \tradersoft\helpers\Form::end()?>
    </div>

</div>