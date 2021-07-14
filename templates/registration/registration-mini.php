<?php
/** @var $model \tradersoft\model\Registration_Mini */
$model = TSInit::$app->getVar('registrationModel');
/** @var $session \tradersoft\helpers\Session */
$session =TSInit::$app->session;
/** @var $showAllCountries bool */
$showAllCountries = TSInit::$app->getVar('showAllCountries');
/** @var $enableCaptcha string */
$enableCaptcha = TSInit::$app->getVar('enableCaptcha');
/** @var $filterByCountryType string */
$filterByCountryType = TSInit::$app->getVar('filterByCountryType');
?>

<div class="title"><?php echo \TS_Functions::__('Open Free Account'); ?></div>
<div class="form">
    <?php if ($session->hasFlash('error_registration')): ?>
        <div class="error"><?php echo $session->getFlash('error_registration'); ?></div>
    <?php endif; ?>
    <?php /** @var $form \tradersoft\helpers\Form */?>
    <?php $form = \tradersoft\helpers\Form::begin($model, ['htmlOptions' => ['id'=>'reg_form']])?>
        <?php echo \tradersoft\helpers\Html::hiddenInput('tradersoft_submit', 'registration_mini'); ?>
        <?php echo $form->field('fullname'); ?>
        <?php echo $form->field('email'); ?>
        <?php echo $form->field('phone'); ?>
        <?php echo $form->field('country')->dropDownList(
            $model->getCountriesList($showAllCountries, $filterByCountryType),
            [
                'id' => 'registration-mini-country',
                'options' => $model->getCountriesOptions($showAllCountries, $filterByCountryType)
            ]
        ); ?>
        <?php echo $form->field('accept', [
            'options' => ['class' => 'form-row checkbox-block']
        ])->checkbox([
            'label' => \TS_Functions::__('I am over 18 years of age and I accept the ') . '<a href="' . TS_Functions::getTermsLink() . '">' . \TS_Functions::__('Terms & Conditions') . '</a>',
            'class' => 'checkbox'
        ]) ?>
        <?php if ($model->withCreateExternalAccount()) { ?>
            <?php echo $form->field('createExternalAccount', [
                'options' => ['class' => 'form-row checkbox-block']
            ])->checkbox([
                'label' => \TS_Functions::__('I would like to open an account on ') . $model->withCreateExternalAccount(),
                'class' => 'checkbox',
                'checked' => 'checked'
            ]) ?>
        <?php } ?>
        <?php if ($model->withReceiveEmailNewslettersAgreement()) { ?>
            <?php echo $form->field('agreedReceiveNewsletters', [
                'options' => ['class' => 'form-row checkbox-block']
            ])->checkbox([
                'label' => \TS_Functions::__('Agree to receive updates and offers from us'),
                'class' => 'checkbox',
                'checked' => 'checked'
            ]) ?>
        <?php } ?>
        <?php if ($model->withPrivacyPolicy()) { ?>
            <?php
            echo $form->field('agreedPrivacyPolicy', [
                'options' => ['class' => 'form-row checkbox-block']
            ])->checkbox([
                'label' => \TS_Functions::__('I agree to the ') . '<a href="' . \tradersoft\helpers\Link::getForPageWithKey('[TS-PRIVACY_POLICY]') . '">' . \TS_Functions::__('Privacy Policy') . '</a>',
                'class' => 'checkbox'
            ]);
            ?>
        <?php } ?>

        <?php echo \tradersoft\helpers\Html::submitInput(\TS_Functions::__('Sign up'), [
            'class' => 'btn btn-default btn-large',
            'id' => 'regFormSubmit',
        ]) ?>
        <?php if ($enableCaptcha) { ?>
            <?php echo $form->field('captcha')->captcha($enableCaptcha) ?>
        <?php } ?>
    <?php \tradersoft\helpers\Form::end() ?>
</div>