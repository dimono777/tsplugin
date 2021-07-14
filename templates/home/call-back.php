<?php
/**
 * @var $modelCallBack \tradersoft\model\Call_Back
 * @var $responseCallBack array
 */

$modelCallBack = TSInit::$app->getVar('modelCallBack');
$response = TSInit::$app->getVar('responseCallBack');
$callBackFormOptions = TSInit::$app->getVar('callBackFormOptions', []);
?>

<div class="call-back-form-block">
    <div id="callBackMessage" class="callback-form-message"><?php echo $response['message']?></div>

    <?php /** @var $form \tradersoft\helpers\Form */?>
    <?php $form = \tradersoft\helpers\Form::begin($modelCallBack, $callBackFormOptions)?>
    <?php echo $form->field('tradersoft_submit')->hiddenInput(['value' => 'call_back']); ?>
    <?php echo $form->field('fullName'); ?>
    <?php echo $form->field('email'); ?>
    <div class="form-row">
        <?php echo $form->field('phoneCode', ['options' => ['class' => 'phone-code']])->textInput(['placeholder' => '+000']); ?>
        <?php echo $form->field('phone', ['options' => ['class' => 'phone-number']])->textInput(['placeholder' => \TS_Functions::__('Phone number')]); ?>
    </div>

    <?php
    echo $form->field('country')
        ->dropDownList(
            $modelCallBack->getCountries(),
            [
                'id'        => 'countries_list',
                'class'     => 'olgs_input_select_country',
            ]
        );
    ?>
    <?php echo $form->field('captcha')->captcha() ?>
    <div class="form-row">
        <input id="captchaButton" type="submit" value="<?php echo \TS_Functions::__('Call me back') ?>" class="btn btn-default btn-big">
    </div>
    <?php \tradersoft\helpers\Form::end()?>

</div>