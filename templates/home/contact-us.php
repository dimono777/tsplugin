<?php
/**
 * @var $modelContactUs \tradersoft\model\Contact_Us
 * @var $form \tradersoft\helpers\Form
 */

$modelContactUs = TSInit::$app->getVar('modelContactUs');
?>

<div class="contact-us-block">
    <?php if ($modelContactUs->successes) { ?>
        <div style="color: green; padding-bottom: 10px;"><?php echo $modelContactUs->responseMessage; ?></div>
    <?php } else { ?>
        <div class="error" style="color: red; padding-bottom: 10px;"><?php echo $modelContactUs->responseMessage; ?></div>
    <?php } ?>

    <?php $form = \tradersoft\helpers\Form::begin($modelContactUs)?>
        <?php echo $form->field('tradersoft_submit')->hiddenInput(['value' => 'contact_us']); ?>
        <?php echo $form->field('fullName'); ?>
        <?php echo $form->field('email'); ?>
        <?php echo $form->field('phone'); ?>
        <?php echo $form->field('topic')->dropDownList($modelContactUs->getContactUsTopics(), ['prompt' => \TS_Functions::__('Topic')]); ?>
        <?php echo $form->field('message')->textarea(['placeholder' => \TS_Functions::__('What can we help you with?')]); ?>
        <?php echo tradersoft\helpers\Html::button(\TS_Functions::__('Send'), ['class' => 'btn-default', 'type' => 'submit'])?>
    <?php \tradersoft\helpers\Form::end()?>
</div>