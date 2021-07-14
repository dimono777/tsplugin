<?php
use tradersoft\helpers\Form;
use tradersoft\helpers\Html;

/** @var $model \tradersoft\model\account_details\Base */
$model = TSInit::$app->getVar('accountDetailsModel');
$session = TSInit::$app->session;
?>

<style>
    .success-details {
        color: green;
        padding-bottom: 10px;
    }
</style>
<div class="form-wrap">

    <div class="account_details_form">
        <h3><?php echo TS_Functions::__('Update account details') ?></h3>
        <?php if ($session->hasFlash('error_account_details')): ?>
            <div class="error"><?php echo $session->getFlash('error_account_details'); ?></div>
        <?php endif; ?>
        <?php if ($session->hasFlash('success_account_details')): ?>
            <div class="success-details"><?php echo $session->getFlash('success_account_details'); ?></div>
        <?php endif; ?>

        <?php /** @var $form Form */?>
        <?php $form = Form::begin($model)?>
        <?php echo $form->field('tradersoft_submit')->hiddenInput(['value'=>'account_details']); ?>
        <div class='form-blocks col-lg-5 col-md-5 col-sm-5 col-md-offset-1'>
            <?php echo $form->field('fname'); ?>
            <?php echo $form->field('lname'); ?>
            <div class="form-row form-row-birthday">
                <?php echo $form->field('dayNumber', ['options' => ['class' => 'form-row-select form-row-day']])
                    ->dropDownList($model->getDays(), ['class' => 'olgs_input_select_day', 'prompt' => ''])->label(TS_Functions::__('Day'));
                ?>
                <?php echo $form->field('monthNumber', ['options' => ['class' => 'form-row-select form-row-month']])
                    ->dropDownList($model->getMonths(), ['class' => 'olgs_input_select_month', 'prompt' => ''])->label(TS_Functions::__('Month'));
                ?>
                <?php echo $form->field('yearNumber', ['options' => ['class' => 'form-row-select form-row-year']])
                    ->dropDownList($model->getYears(), ['class' => 'olgs_input_select_year', 'prompt' => ''])->label(TS_Functions::__('Year'));
                ?>
            </div>
            <?php echo $form->field('phone'); ?>
            <?php echo $form->field('cellphone'); ?>
            <?php echo $form->field('email')->textInput(['disabled'=>'disabled']); ?>
        </div>
        <div class='form-blocks col-lg-5 col-md-5 col-sm-5'>
            <?php echo $form->field('address'); ?>
            <?php echo $form->field('address2'); ?>
            <?php echo $form->field('town'); ?>
            <?php echo $form->field('country')->textInput(['disabled'=>'disabled']);; ?>
            <?php echo $form->field('postalCode'); ?>
        </div>
        <div class='form-blocks col-lg-5 col-md-5 col-sm-5'>
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
            <?php echo Html::submitInput(TS_Functions::__('Update details')); ?>
        </div>
        <?php Form::end()?>
    </div>
</div>