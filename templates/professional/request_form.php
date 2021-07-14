<?php

use tradersoft\helpers\Html;

?>
<script>
    var suitabilityApplicationSettings = {
        minNumberAnsweredQuestions: <?= TSInit::$app->getVar('minNumberAnsweredQuestions') ?>
    };
</script>


<div class="professional-request-form">
    
    <h3><?php echo TS_Functions::__('Apply to become a Professional Client in order to trade with your current market rates');?></h3>

    <form id="professional-request-form" method="post" action="/<?= TSInit::$app->getVar('sendRequestUrl') ?>">
        <div>
            <div class="title"><?php echo TS_Functions::__('Qualifying Criteria');?></div>
            
            <?php foreach (TSInit::$app->getVar('questions', []) as $questionId => $questionText) { ?>
                <input
                        type="checkbox"
                        name="questions[<?= $questionId ?>]"
                        id="question_<?= $questionId ?>"
                        value="<?= $questionId ?>"
                >
                <label for="question_<?= $questionId ?>"><?= Html::encode(TS_Functions::__($questionText)) ?></label>
            <?php } ?>

            <input type="submit" id="submitButton" class="btn btn-default btn-large center" value="<?php echo TS_Functions::__('Apply');?>" disabled="disabled">
        </div>
    </form>
    
</div>