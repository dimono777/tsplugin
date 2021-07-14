<?php
/** @var $partnersRegistrationModel \tradersoft\model\Partners_Registration */
$partnersRegistrationModel = TSInit::$app->getVar('partnersRegistrationModel');
/** @var $session \tradersoft\helpers\Session */
$session =TSInit::$app->session;
?>

<?php
if ($session->hasFlash('after-registration')) {
    require_once dirname(__FILE__) . '/after-registration.php';
} else {
?>
<div class="form-wrap">
    <div class="register_form">
        <?php if ($session->hasFlash('error_partners_registration')): ?>
            <div class="error"><?php echo $session->getFlash('error_partners_registration'); ?></div>
        <?php endif; ?>

        <?php /** @var $form \tradersoft\helpers\Form */?>
        <?php $form = \tradersoft\helpers\Form::begin($partnersRegistrationModel)?>
            <?php echo \tradersoft\helpers\Html::hiddenInput('tradersoft_submit', 'partner_registration'); ?>
            <div class='form-blocks'>
                <?php echo $form->field('fname'); ?>
                <?php echo $form->field('lname'); ?>
                <?php echo $form->field('language')->dropDownList($partnersRegistrationModel->getLanguages(),['class'=>'select_country_language']); ?>
                <?php echo $form->field('phone'); ?>
            </div>
            <div class='form-blocks'>
                <?php echo $form->field('email'); ?>
                <?php
                    echo $form->field('password', [
                            'template' => "{label}\n{input}\n{error}\n{htmlBlock}",
                    ])->passwordInput()
                        ->htmlBlock('
                        <div class="password-rules">
                            <ul>
                                <li>'. \TS_Functions::__('Use 5-15 characters').'</li>
                                <li>'. \TS_Functions::__('You can use the next symbols:').' 
                                    <span>! " # $ % & &#39; ( ) * + , - . / : ; < = > ? @ [ \ ] ^ _ ` { | } ~</span></li>
                                <li>'. \TS_Functions::__('Combine letters (capital and lower case)').'
                                    '.\TS_Functions::__('with numbers').'</li>
                                <li>'. \TS_Functions::__('Avoid common passwords like 1234, "password", or your name').'</li>
                                <li>'. \TS_Functions::__('Don’t use gap in password').'</li>
                            </ul>
                        </div>
                    ');
                ?>
                <?php echo $form->field('confirmPassword')->passwordInput(); ?>
                <?php echo $form->field('accept', [
                        'options' => ['class' => 'form-row checkbox-block']
                    ])->checkbox([
                            'label' => \TS_Functions::__('I am over 18 years of age and I accept the ') .
                                '<a href="' . \tradersoft\helpers\Partner::getPartnerTermsLink() . '">' . \TS_Functions::__('Terms & Conditions') . '</a>',
                            'class' => 'checkbox'
                    ]); ?>
            </div>
            <div class="center">
                <?php echo \tradersoft\helpers\Html::submitInput(\TS_Functions::__('Sign up'), ['class'=>'btn btn-orange btn-medium']); ?>
                <span>
                    <?php echo \TS_Functions::__('Already have an account?');?>
                    <a href="<?php echo \tradersoft\helpers\Partner::getPartnerAuthLink()?>"><?php echo \TS_Functions::__('Log In');?></a>
                </span>
            </div>
        <?php \tradersoft\helpers\Form::end()?>

    </div>
</div>
<?php } ?>
