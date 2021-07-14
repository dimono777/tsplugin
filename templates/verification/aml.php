<?php

use tradersoft\helpers\Arr;
use tradersoft\helpers\Form;
use tradersoft\helpers\Html;
use tradersoft\helpers\system\Translate;
use tradersoft\model\verification\aml\Form as AMLVerificationForm;

/** @var AMLVerificationForm $model */
$model = TSInit::$app->getVar('model');

/** @var string $errorMessage */
$errorMessage = TSInit::$app->getVar('errorMessage');
?>

<div class="form-wrap">
    <div class="aml-verification-form">
        <?php if ($errorMessage): ?>
            <div class="error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <?php $form = Form::begin($model) ?>
        <?php echo Html::hiddenInput('tradersoft_submit', $model->formName()); ?>

        <?php echo $form->field('countryOfTax')
            ->dropDownList($model->getTaxCountries(), [
                'prompt' => $model->getAttributeLabel('countryOfTax')
            ]);
        ?>

        <?php echo $form->field('TIN', [
            'options' => [
                'class' => 'js-field-to-skip',
            ],
            'template' => "{label}\n{input}\n{htmlBlock}\n{error}",
        ])->htmlBlock(
            '<div class="tooltip-tin-icon"><svg height="12" width="12" stroke="#000" viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg"><g><circle cx="60" cy="59.857144" fill="none" r="56" stroke-width="12" /><line fill="none" fill-opacity="null" stroke-linecap="null" stroke-linejoin="null" stroke-opacity="null" stroke-width="16" x1="60" x2="60" y1="100.476191" y2="50.238094" /><line fill="none" fill-opacity="null" stroke-linecap="null" stroke-linejoin="null" stroke-opacity="null" stroke-width="16" x1="59.999999" x2="59.999999" y1="37" y2="22" transform="rotate(-0.000005362965566746425 60.00000009781275,29.4999999985337) "/></g></svg></div>'
            . '<div class="tooltip-tin-wrapper">'
            . '<p>' . Translate::__('The term “TIN” means Taxpayer Identification Number or a functional equivalent in the absence of a TIN. A TIN is a unique combination of letters or numbers assigned by a jurisdiction to an individual or an Entity and used to identify the individual or Entity for the purposes of administering the tax laws of such jurisdiction. Further details of acceptable TINs can be found at :link', [':link' => '<a href="http://www.oecd.org/tax/transparency/automaticexchangeofinformation.htm" target="_blank">http://www.oecd.org/tax/transparency/automaticexchangeofinformation.htm</a>']) . '</p>'
            . '<p>' . TS_Functions::__('Some jurisdictions do not issue a TIN. However, these jurisdictions often utilise some other high integrity number with an equivalent level of identification (a “functional equivalent”). Examples of that type of number include, for individuals, a social security(SSN)/insurance number, citizen/personal identification/service code/number, and resident registration number.') . '</p>'
            . '</div>'
        ); ?>

        <?php echo $form->field('doNotHaveTIN', [
            'options' => ['class' => 'checkbox-block']
        ])->checkbox([
            'label' => $model->getAttributeLabel('doNotHaveTIN'),
            'class' => 'js-skip-field-control',
        ]);
        ?>
        <div class="form-row field-aml-verification-donothavetin-box js-skipped-field-alternative">
            <?php foreach ($model->getTINMissingReasons() as $reason) { ?>
                <?php echo $form->field('TINMissingReasonId', [
                'options' => ['class' => 'checkbox-block']
            ])->radio([
                'id' => 'TINMissingReasonId' . $reason->id,
                'value' => $reason->id,
                'label' => TS_Functions::__($reason->title . ' - ' . $reason->description),
                'uncheck' => null,
            ])
                ?>
                <?php if($reason->hasComment) { ?>
                    <?php echo $form->field('TINMissingReasonComment[' . $reason->id . ']', ['options' => ['class' => 'textarea-show']])
                    ->textarea([
                        'value' => Arr::get($model->TINMissingReasonComment, $reason->id),
                        'placeholder' => Translate::__('Please explain in the following boxes why you are unable to obtain a SSN/TIN if you selected :reason above.', [
                            ':reason' => TS_Functions::__($reason->title),
                        ])
                    ])
                ?>
                <?php } ?>
            <?php } ?>
        </div>

        <?php echo $form->field('nationalIdentificationNumber'); ?>
        
        <?php echo $form->field('companyName'); ?>

        <?php echo $form->field('declarationOfKnowledgeBelief', [
            'options' => ['class' => 'checkbox-block'],
        ])->checkbox([
            'value' => $model->getAttributeLabel('declarationOfKnowledgeBelief'),
            'label' => $model->getAttributeLabel('declarationOfKnowledgeBelief'),
            'class' => 'js-required-field',
        ]);
        ?>

        <?php echo $form->field('consentCooperationTaxAuthorities', [
            'options' => ['class' => 'checkbox-block'],
        ])->checkbox([
            'value' => $model->getAttributeLabel('consentCooperationTaxAuthorities'),
            'label' => $model->getAttributeLabel('consentCooperationTaxAuthorities'),
            'class' => 'js-required-field',
        ]);
        ?>

        <p class="aml-verification-info"><?php echo TS_Functions::__('Please note that we will not use this information to contact your employer'); ?></p>
        
        <div class="form-row">
            <?php echo Html::submitInput(TS_Functions::__('Submit'), ['class' => 'form-submit-button']); ?>
        </div>
        <?php Form::end() ?>
    </div>
</div>