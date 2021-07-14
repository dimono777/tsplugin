<?php
use tradersoft\helpers\Link;
use \tradersoft\components\DataPolicyRegistration;
/**
 * Registration form
 *
 * @var \tradersoft\model\Base_Registration $model
 * @var \tradersoft\helpers\Session $session
 * @var bool $showAllCountries
 * @var bool $filterByCountryType
 * @var bool $allowPromoCode
 * @var string $enableCaptcha
 */
?>

<div class="register_form">
    <?php if ($session->hasFlash('error_registration')): ?>
        <div class="error"><?php echo $session->getFlash('error_registration'); ?></div>
    <?php endif; ?>

    <?php /** @var $form \tradersoft\helpers\Form */?>
    <?php $form = \tradersoft\helpers\Form::begin($model, ['htmlOptions' => ['id'=>'reg_form']])?>
    <?php echo \tradersoft\helpers\Html::hiddenInput('tradersoft_submit', 'registration'); ?>
    <div class='form-blocks'>
        <?php echo $form->field('fname'); ?>
        <?php echo $form->field('lname'); ?>
        <?php
            echo $form->field('country')
                ->dropDownList(
                    $model->getCountriesList($showAllCountries, $filterByCountryType),
                    [
                        'id' => 'registration-country',
                        'class'=>'select_country_language',
                        'options'=>$model->getCountriesOptions($showAllCountries, $filterByCountryType),
                        'prompt'=>\TS_Functions::__('Choose the country')
                    ]
                );
        ?>
        <?php if ($filterByCountryType) {?>
            <div class="form-block-hint">
                <?php echo __('If you don`t find your country, please click');?>
                <a href="<?php echo DataPolicyRegistration::getRegistrationAnotherCountryTypeLink();?>"><?php echo __('here');?></a>
            </div>
        <?php }?>
        <div class="form-row form-row-phone">
            <?php
                echo $form->field(
                    'phoneCode',
                    [
                        'options' => ['class'=>'phone-code']
                    ]
                )
                ->textInput(['id' => 'registration-phonecode']);
            ?>
            <?php echo $form->field('phone', ['options' => ['class'=>'phone-number']])->textInput(); ?>
        </div>
    </div>
    <div class='form-blocks'>
        <?php echo $form->field('email'); ?>
        <?php if ($model->withCurrency()) { ?>
            <?php echo $form->field('currency')->dropDownList($model->getCurrencyList()); ?>
        <?php } ?>
        <?php
            echo $form->field(
                    'password',
                    [
                        'template' => "{label}\n{input}\n{error}\n{htmlBlock}",
                    ]
                )
                ->passwordInput()
                ->htmlBlock(
                        '<div class="password-rules">
                            <ul>
                                <li>'. \TS_Functions::__('Use 5-15 characters').'</li>
                                <li>'. \TS_Functions::__('You can use the next symbols:').' 
                                    <span>! " # $ % & &#39; ( ) * + , - . / : ; < = > ? @ [ \ ] ^ _ ` { | } ~</span></li>
                                <li>'. \TS_Functions::__('Combine letters (capital and lower case) with numbers').'</li>
                                <li>'. \TS_Functions::__('Avoid common passwords like 1234, "password", or your name').'</li>
                                <li>'. \TS_Functions::__('Don&#39;t use gap in password').'</li>
                            </ul>
                        </div>'
                );
        ?>
        <?php echo $form->field('confirmPassword')->passwordInput(); ?>
        <?php if ($allowPromoCode) { ?>
            <?php echo $form->field('promoCode')->textInput() ?>
        <?php } else { ?>
            <?php echo $form->field('accept', [
                    'options' => ['class' => 'form-row checkbox-block']
                ])->checkbox([
                    'label' => \TS_Functions::__('I am over 18 years of age and I accept the ') . '<a href="' . TS_Functions::getTermsLink() . '">' . \TS_Functions::__('Terms & Conditions') . '</a>',
                    'class' => 'checkbox'
                ]);
            ?>
        <?php } ?>

        <?php if ($model->withCreateExternalAccount()) { ?>
            <?php echo $form->field('createExternalAccount', [
                'options' => ['class' => 'form-row checkbox-block']
            ])->checkbox([
                'label' => \TS_Functions::__('I would like to open an account on ') . $model->withCreateExternalAccount(),
                'class' => 'checkbox',
                'checked' => 'checked'
            ]) ?>
        <?php } ?>

        <?php if ($model->withNotUSReportablePerson()) { ?>
            <?php
                echo $form->field('notUSReportablePerson', [
                    'options' => ['class' => 'form-row checkbox-block']
                ])->checkbox([
                    'label' => \TS_Functions::__('I declare that I am not a US reportable person for the purposes of FATCA'),
                    'class' => 'checkbox'
                ]);
            ?>
        <?php } ?>

        <?php if ($model->withReceiveEmailNewslettersAgreement()) { ?>
            <?php
                echo $form->field('agreedReceiveNewsletters', [
                    'options' => ['class' => 'form-row checkbox-block']
                ])->checkbox([
                    'label' => \TS_Functions::__('Agree to receive updates and offers from us'),
                    'class' => 'checkbox',
                    'checked' => 'checked'
                ]);
            ?>
        <?php } ?>

        <?php if ($model->withPrivacyPolicy()) { ?>
            <?php
            echo $form->field('agreedPrivacyPolicy', [
                'options' => ['class' => 'form-row checkbox-block']
            ])->checkbox([
                'label' => \TS_Functions::__('I agree to the ') . '<a href="' . Link::getForPageWithKey('[TS-PRIVACY_POLICY]') . '">' . \TS_Functions::__('Privacy Policy') . '</a>',
                'class' => 'checkbox'
            ]);
            ?>
        <?php } ?>

    </div>
    <?php if ($allowPromoCode) { ?>
        <div class="form-blocks--terms">
            <?php echo $form->field('accept', [
                'options' => ['class' => 'form-row checkbox-block']
            ])->checkbox([
                'label' => \TS_Functions::__('I am over 18 years of age and I accept the ') . '<a href="' . TS_Functions::getTermsLink() . '">' . \TS_Functions::__('Terms & Conditions') . '</a>',
                'class' => 'checkbox'
            ]) ?>
        </div>
    <?php } ?>
    <div class="center">
        <?php echo \tradersoft\helpers\Html::submitInput(\TS_Functions::__('Sign up'), [
            'class' => 'btn btn-orange btn-medium',
            'id' => 'regFormSubmit',
        ]) ?>
        <span>
            <?php echo \TS_Functions::__('Already have an account?');?>
            <?php echo TS_Functions::getAuthorisationLinkHtml(); ?>
        </span>
        <?php if ($enableCaptcha) { ?>
            <?php echo $form->field('captcha')->captcha($enableCaptcha) ?>
        <?php } ?>
    </div>
    <?php \tradersoft\helpers\Form::end()?>
</div>

<script>
  (function($) {
    $(document).ready(function() {
      $('#registration-country').on('change', function() {
        var phoneCodeElem = $('#registration-phonecode');
        var phoneCode = $('option:selected', this).attr('data-target');
        if (phoneCode !== 'undefined' && phoneCodeElem[0] !== 'undefined') {
          phoneCodeElem.val(phoneCode);
        }
      
        if ($('option:selected', this).data('invalid') == 1) {
          $('option:selected', this).each(function() {
            this.selected = false;
          });
          //for invalid countries popup
          $(document).trigger("invalidCountriesChanged");
        }
      });
    });
  })(window.jQuery);
</script>