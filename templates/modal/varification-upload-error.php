<?php
use tradersoft\helpers\Form;
use tradersoft\helpers\Html;
/**
 * @var array $messages
 */
/** @var \tradersoft\model\Model $model */
$model = TSInit::$app->getVar('modalVerificationUploadModel');

?>
<div id="js_verPopup" class="ver-popup-wrapper">
    <div class="box-modal-wrapper">
		<button id="js_hide" class="ver-popup-close">
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 174.239 174.239" style="enable-background:new 0 0 174.239 174.239;" xml:space="preserve" width="512px" height="512px">
				<g>
					<path d="M87.12,0C39.082,0,0,39.082,0,87.12s39.082,87.12,87.12,87.12s87.12-39.082,87.12-87.12S135.157,0,87.12,0z M87.12,159.305   c-39.802,0-72.185-32.383-72.185-72.185S47.318,14.935,87.12,14.935s72.185,32.383,72.185,72.185S126.921,159.305,87.12,159.305z" fill="#cccccc"/>
					<path d="M120.83,53.414c-2.917-2.917-7.647-2.917-10.559,0L87.12,76.568L63.969,53.414c-2.917-2.917-7.642-2.917-10.559,0   s-2.917,7.642,0,10.559l23.151,23.153L53.409,110.28c-2.917,2.917-2.917,7.642,0,10.559c1.458,1.458,3.369,2.188,5.28,2.188   c1.911,0,3.824-0.729,5.28-2.188L87.12,97.686l23.151,23.153c1.458,1.458,3.369,2.188,5.28,2.188c1.911,0,3.821-0.729,5.28-2.188   c2.917-2.917,2.917-7.642,0-10.559L97.679,87.127l23.151-23.153C123.747,61.057,123.747,56.331,120.83,53.414z" fill="#cccccc"/>
				</g>
			</svg>
		</button>
		
		<h2 class="box-modal-title error"><?php echo \TS_Functions::__('Upload error!'); ?></h2>

		<div class="box-modal-description">
			<p><?php echo current($messages); ?></p>
		</div>

		<div class="buttons-wrap">
			<?php
            $form = Form::begin(
                $model,
                [
                    'enableClientValidation' => false,
                    'htmlOptions'            => [
                        'id'      => 'verification-upload-popup',
                        'enctype' => 'multipart/form-data',
                    ],
                ]
            );
            ?>
			<?php echo Html::hiddenInput('tradersoft_submit', 'verificationUpload'); ?>
            <?php
            echo $form
                ->field('file',
                        [
                            'options' => [
                                'id' => 'verification_upload-file-popup',
                            ],
                            'template' => "{label}\n<div class=\"upload-button-wrap\">{input}<a href=\"#\" class=\"upload-grey-button\"><span>" . \TS_Functions::__('Upload Another Document') . "</span></a><div class=\"fixed-loader\"></div></div>\n{error}",
                        ]
                )
                ->fileInput(
                    [
                        'id' => 'verification_upload-file-popup-input',
                    ]
                );
            ?>
			<?php Form::end()?>
		</div>
    </div>
</div>

<script>
// Close Pop UP
(function initVerPopUP () {
	const closePopUP = document.querySelector('#js_hide');
	const verPopup = document.querySelector('#js_verPopup');

	function closeVerPopUP () {
		verPopup.style.display = 'none'
	}

	closePopUP.addEventListener("click", closeVerPopUP);
})();
</script>