<?php

use tradersoft\helpers\Form;
use tradersoft\helpers\Html;

/**
 * @var $model   \tradersoft\model\Verification_Upload
 * @var $session \tradersoft\helpers\Session
 * @var $form    \tradersoft\helpers\Form
 */

$session = TSInit::$app->session;
$model = TSInit::$app->getVar('verificationUploadModel');
$maxUploadFileSize = TSInit::$app->getVar('maxUploadFileSize');
$messageBlock = TSInit::$app->getVar('messageBlock', '');
$types = TSInit::$app->getVar('types');
$comments = TSInit::$app->getVar('mandatorycomments');
$commentlabel = TSInit::$app->getVar('commentlabel');
$uploadbuttonlabel = TSInit::$app->getVar('uploadbuttonlabel');
$uploadanotherbuttonlabel = TSInit::$app->getVar('uploadanotherbuttonlabel');
$csrf = TSInit::$app->getVar('csrf');
$signature = TSInit::$app->getVar('signature');
$canUploadDocuments = TSInit::$app->getVar('canUploadDocuments');

$formUuid = wp_rand();
?>

    <div class="form-verification">
        <div class="form-verification__col form-verification__col_flex">

            <?php

            $form = Form::begin(
                $model,
                [
                    'enableClientValidation' => false,
                    'htmlOptions' => ['id' => 'verification-upload-' . $formUuid, 'enctype' => 'multipart/form-data'],
                ]
            );

            $commentParams = [
                'id' => 'verification_upload_comment-' . $formUuid,
                'class' => 'form-verification__comment',
                'placeholder' => ''
            ];

            echo Html::hiddenInput('tradersoft_submit', 'verificationUpload');
            echo Html::hiddenInput('csrf', $csrf);
            echo Html::hiddenInput('signature', $signature);

            if ($canUploadDocuments && count($types) > 1) {
                if (!empty($comments)) {
                    echo $form
                        ->field('comment', ['options' => ['class' => 'form-verification__comment-box']])
                        ->textInput($commentParams)
                        ->label(\TS_Functions::__($commentlabel));
                }

                echo $form
                    ->field('categoryTypeId', ['options' => ['class' => 'form-verification__select-box']])
                    ->dropDownList(
                        array_map(
                            static function ($type) {
                                return \TS_Functions::__($type);
                            },
                            $types
                        ),
                        [
                            'id' => 'verification_upload-' . $formUuid,
                            'class' => 'form-verification__select',
                            'required' => true,
                        ]
                    );

                echo $form
                    ->field(
                        'file',
                        [
                            'template' => '{label}
                <div type="button" class="form-verification__btn" id="buttonUpload-' . $formUuid . '">
                    {input}
                    <span id="fileUpload-' . $formUuid . '">
                        <span class="simply-text">' . \TS_Functions::__($uploadbuttonlabel) . '</span>
                        <span class="loading-text">' . \TS_Functions::__('Loading') . '</span>
                        <span class="text">' . \TS_Functions::__($uploadanotherbuttonlabel) . '</span>
                    </span>
                </div>',
                        ]
                    )->fileInput();
            } else {
                if ($canUploadDocuments && count($types) === 1) {
                    echo Html::hiddenInput('categoryTypeId', key($types));
                    if (in_array(key($types), $comments)) {
                        echo $form
                            ->field('comment', ['options' => ['class' => 'form-verification__comment-box']])
                            ->input($commentParams)
                            ->label(\TS_Functions::__($commentlabel));
                    }
                }
                echo $form->field(
                    'file',
                    [
                        'template' => '{label}
                <div class="upload-button-box ">
                    <div id="buttonUpload-' . $formUuid . '" type="button" class="form-verification__btn  form-verification__btn_single'. (!$canUploadDocuments ? ' disabled' : '') .'">
                        {input}
                        <span id="fileUpload-' . $formUuid . '">
                            <span class="simply-text">' . \TS_Functions::__($uploadbuttonlabel) . '</span>
                            <span class="loading-text">' . \TS_Functions::__('Loading') . '</span>
                            <span class="text">' . \TS_Functions::__($uploadanotherbuttonlabel) . '</span>
                        </span>
                    </div>
                </div>',
                    ]
                )->fileInput();
            }
            Form::end();
            ?>

            <ul class="form-verification__list" id="fileList-<?= $formUuid ?>"></ul>
            <div class="form-verification__col" id="notificationBox-<?= $formUuid ?>">
                <div class="form-verification___alert form-verification__alert_success"
                     id="notificationSuccess-<?= $formUuid ?>" hidden>
                    <h6 class="form-verification__alert-title"><?= TS_Functions::__('Document submitted.') ?></h6>
                    <span class="text"><?= TS_Functions::__(
                            'Thank you for uploading your document. The document(s) will be checked soon and we will inform you via email about the results of the verification.'
                        ) ?></span>
                </div>
                <div class="form-verification___alert form-verification__alert_error"
                     id="notificationError-<?= $formUuid ?>" hidden>
                    <h6 class="form-verification__alert-title">
                        <span id="errorTitle-<?= $formUuid ?>" class="error-title"></span>
                        <?= TS_Functions::__('Upload error!') ?>
                    </h6>
                    <span class="text" id="errorDescription-<?= $formUuid ?>"></span>
                </div>
                <div class="form-verification__notice"></div>
            </div>

            <div id="scrollUpload-<?= $formUuid ?>"></div>
        </div>
    </div>

    <script>
          jQuery(document).ready(function() {
                var uploader<?= $formUuid ?> = new Uploader({
                    maxUploadFileSize: <?= $maxUploadFileSize ?>,
                    formUrl: '<?= $_SERVER['REQUEST_URI'] ?>',
                    maxSizeErrorText:'<?= isset($model->getValidators('file')[0][1]['messages']['tooBig']) ? $model->getValidators('file')[0][1]['messages']['tooBig'] : TS_Functions::__('Maximum file size is&nbsp;') ?>',
                    canUploadDocuments:  (new Boolean(<?= $canUploadDocuments; ?>)).valueOf(),
                    forbidUploadDocumentsReason: '<?= TS_Functions::__(TSInit::$app->getVar('forbidUploadDocumentsReason')); ?>',
                    formSelector: '#verification-upload-<?= $formUuid ?>',
                    commentsArr: <?= json_encode($comments); ?>,
                    commentBox: '.field-verification_upload_comment-<?= $formUuid ?>',
                    comment: '#verification_upload_comment-<?= $formUuid ?>',
                    inputSelector: 'input[type="file"]',
                    select: '#verification_upload-<?= $formUuid ?>',
                    buttonUpload: '#buttonUpload-<?= $formUuid ?>',
                    fileUpload: '#fileUpload-<?= $formUuid ?>',
                    notificationSuccess: '#notificationSuccess-<?= $formUuid ?>',
                    notificationError: '#notificationError-<?= $formUuid ?>',
                    errorTitle: '#errorTitle-<?= $formUuid ?>',
                    errorDescription: '#errorDescription-<?= $formUuid ?>',
                    haveCategoryTypes: <?= count($types) > 1 ? 'true' : 'false' ?>,
                    scrollUpload: '#scrollUpload-<?= $formUuid ?>',
                    fileList: '#fileList-<?= $formUuid ?>',
                    notificationBox: '#notificationBox-<?= $formUuid ?>',
                    iconSvg: '<svg width="12px" height="12px" viewBox="0 0 12 12" version="1.1" xmlns="http://www.w3.org/2000/svg">\n' +
              '    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\n' +
              '        <g id="4.0_upload-another-doc" transform="translate(-32.000000, -1008.000000)" fill="currentColor" fill-rule="nonzero">\n' +
              '            <g id="paper-clip-outline" transform="translate(38.000000, 1014.000000) rotate(-90.000000) translate(-38.000000, -1014.000000) translate(32.000000, 1008.000000)">\n' +
              '                <path d="M11.14284,7.98153776 L6.16647,3.42648055 C5.81808,3.09718078 5.39271,2.93255835 4.89024,2.93255835 C4.422,2.93255835 4.02525,3.08150114 3.69972,3.37944165 C3.3741,3.67732723 3.21141,4.04062243 3.21141,4.46924485 C3.21141,4.92919908 3.39129,5.3186087 3.75102,5.63741876 L7.26282,8.85182609 C7.31988,8.90408238 7.38285,8.9301968 7.45131,8.9301968 C7.54269,8.9301968 7.67688,8.84910755 7.85376,8.6871762 C8.03073,8.52518993 8.11914,8.40225172 8.11914,8.3186087 C8.11914,8.25602746 8.09073,8.19849886 8.03361,8.14618764 L4.52193,4.93178032 C4.37919,4.79063616 4.30782,4.63647597 4.30782,4.46924485 C4.30782,4.31758352 4.36203,4.19217391 4.47054,4.09287872 C4.57899,3.99355606 4.71612,3.94393593 4.88172,3.94393593 C5.07585,3.94393593 5.24712,4.00657208 5.39565,4.13200915 L10.3719,8.68706636 C10.73184,9.01625629 10.91172,9.39514874 10.91172,9.82368879 C10.91172,10.1583982 10.79169,10.4353318 10.55196,10.6547918 C10.31196,10.8743066 10.0095,10.9842563 9.64389,10.9842563 C9.17565,10.9842563 8.76171,10.8194142 8.40216,10.4901419 L1.75539,4.39848055 C1.32141,4.00127231 1.10454,3.52821968 1.10454,2.97940503 C1.10454,2.42534554 1.31289,1.95229291 1.72977,1.56032952 C2.14659,1.16842105 2.65761,0.972384439 3.26295,0.972384439 C3.84531,0.972384439 4.36203,1.17361098 4.81314,1.57592677 L10.00368,6.33479176 C10.06074,6.38713043 10.12638,6.41308009 10.20069,6.41308009 C10.29207,6.41308009 10.42491,6.3333913 10.59891,6.17401373 C10.77315,6.01463616 10.86,5.89312586 10.86,5.8095103 C10.86,5.74687414 10.83156,5.68931808 10.77456,5.63706178 L5.59257,0.885967963 C4.93593,0.295359268 4.15656,0 3.25434,0 C2.34642,0 1.57845,0.290141876 0.95034,0.87028833 C0.32217,1.45046224 0.00813,2.15610069 0.00813,2.98709382 C0.00813,3.80243478 0.33069,4.51065446 0.97593,5.1117254 L7.63119,11.1956705 C8.20218,11.718151 8.87301,11.9795973 9.64392,11.9795973 C10.31199,11.9795973 10.8717,11.7730709 11.32269,11.3601281 C11.7738,10.947405 11.99955,10.4350572 11.99955,9.82338673 C11.99937,9.10742334 11.71374,8.49341876 11.14284,7.98153776 Z" id="Path"></path>\n' +
              '            </g>\n' +
              '        </g>\n' +
              '    </g>\n' +
              '</svg>',
            });
            uploader<?= $formUuid ?>.init();
        });
    </script>
<?= $messageBlock ?>