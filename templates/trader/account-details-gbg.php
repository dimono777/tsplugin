<?php

use tradersoft\helpers\Form;
use tradersoft\helpers\Html;
use tradersoft\helpers\Link;

/** @var $model \tradersoft\model\account_details\GBG */
$model = TSInit::$app->getVar('accountDetailsModel');
$session = TSInit::$app->session;
?>

<div class="form-wrap">
    <?php if ($session->hasFlash('autoVerificationStatus')) { ?>
    <div class="alert alert-warning">
        <?php echo TSInit::$app->session->getFlash('autoVerificationStatus')?>
    </div>
    <?php } ?>

    <div class="account_details_form">
        <h3><?php echo TS_Functions::__('Update account details') ?></h3>
        <?php if ($session->hasFlash('error_account_details')): ?>
            <div class="error"><?php echo $session->getFlash('error_account_details'); ?></div>
        <?php endif; ?>
        <?php if ($session->hasFlash('success_account_details')): ?>
            <div style="color: green; padding-bottom: 10px;"><?php echo $session->getFlash('success_account_details'); ?></div>
        <?php endif; ?>

        <?php /** @var $form Form */?>
        <?php $form = Form::begin($model)?>
        <?php echo $form->field('tradersoft_submit')->hiddenInput(['value'=>'account_details']); ?>
        <div class='form-blocks col-lg-5 col-md-5 col-sm-5 col-md-offset-1'>
            <?php echo $form->field('fname')->textInput($model->getAttributeOptions('fname')); ?>
            <?php if (property_exists($model, 'middleName')) { ?>
                <?php echo $form->field('middleName')->textInput($model->getAttributeOptions('middleName')); ?>
            <?php } ?>
            <?php echo $form->field('lname')->textInput($model->getAttributeOptions('lname')); ?>
            <?php echo $form->field('gender')->dropDownList($model->getGenderList(), $model->getAttributeOptions('gender')); ?>
            <div class="form-row form-row-birthday">
                <?php echo $form->field('dayNumber', ['options' => ['class' => 'form-row form-row-select form-row-day']])
                    ->dropDownList($model->getDays(), $model->getAttributeOptions('dayNumber', ['class' => 'olgs_input_select_day', 'prompt' => 'Day']));
                ?>
                <?php echo $form->field('monthNumber', ['options' => ['class' => 'form-row form-row-select form-row-month']])
                    ->dropDownList($model->getMonths(), $model->getAttributeOptions('monthNumber', ['class' => 'olgs_input_select_month', 'prompt' => 'Month']));
                ?>
                <?php echo $form->field('yearNumber', ['options' => ['class' => 'form-row form-row-select form-row-year']])
                    ->dropDownList($model->getYears(), $model->getAttributeOptions('yearNumber', ['class' => 'olgs_input_select_year', 'prompt' => 'Year']));
                ?>
            </div>
            <?php echo $form->field('phone')->textInput($model->getAttributeOptions('phone')); ?>
            <?php echo $form->field('cellphone')->textInput($model->getAttributeOptions('cellphone')); ?>
            <?php echo $form->field('email')->textInput($model->getAttributeOptions('email')); ?>
        </div>
        <div class='form-blocks col-lg-5 col-md-5 col-sm-5'>
            <?php if (property_exists($model, 'nationalId')) { ?>
                <?php echo $form->field('nationalId')->textInput($model->getAttributeOptions('nationalId')); ?>
            <?php } ?>
            <?php if (property_exists($model, 'address')) { ?>
                <?php echo $form->field('address')->textInput($model->getAttributeOptions('address')); ?>
            <?php } ?>
            <?php if (property_exists($model, 'street')) { ?>
                <?php echo $form->field('street')->textInput($model->getAttributeOptions('street')); ?>
            <?php } ?>
            <?php if (property_exists($model, 'building')) { ?>
                <?php echo $form->field('building')->textInput($model->getAttributeOptions('building')); ?>
            <?php } ?>
            <?php echo $form->field('town')->textInput($model->getAttributeOptions('address')); ?>
            <?php echo $form->field('country')->dropDownList($model->getCountriesList(), $model->getAttributeOptions('country')); ?>
            <?php if ($model->isStateEnabled()) { ?>
                <?php echo $form->field('state')->dropDownList($model->getStatesList(), $model->getAttributeOptions('state')); ?>
            <?php } ?>
            <?php echo $form->field('postalCode')->textInput($model->getAttributeOptions('postalCode')); ?>
        </div>
        <div class="form-row">
            <?php if ($model->withReceiveEmailNewslettersAgreement()) {
                echo $form->field('agreedReceiveNewsletters', [
                    'options' => ['class' => 'form-row checkbox-block'],
                ])->checkbox([
                    'label' => TS_Functions::__('Agree to receive updates and offers from us'),
                    'class' => 'checkbox',
                    'checked' => 'checked',
                ]);
            } ?>
        </div>
        <div class="form-row">
            <?php echo Html::submitInput('Save', $model->getAttributeOptions('submit')); ?>
        </div>
        <?php Form::end()?>
    </div>
</div>

<script>
  (function($){
    $(document).ready(function () {
        var $toSupport = $('.to-support');
        $toSupport.on('click', function(event) {
            var $liveChat = $('#scD84A').find('a');
            var $liveAgent = $('.circleRollButton');
            if ($liveAgent.length != 0) {
                $liveAgent.trigger('click');
            } else if ($liveChat.length != 0) {
                $liveChat.trigger('click');
            } else if (typeof(jivo_api) !== 'undefined'){
                jivo_api.open();
            } else {
                window.open('<?php echo Link::getForPageWithKey('[TS-CONTACT-US]')?>', '_blank');
            }
        });
    })
  })(window.jQuery);
</script>