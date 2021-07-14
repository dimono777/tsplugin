<?php
/**
 * @var $session \tradersoft\helpers\Session
 * @var $lang string
 * @var $tree string
 * @var $totalPagesCount int
 * @var $pageId int
 * @var $haveValidationFails bool
 */

$session = TSInit::$app->session;
$surveyData = TSInit::$app->getVar('surveyData');
extract($surveyData);
?>

<?php if ($session->hasFlash('surveySuccess')) { ?>

    <div class="main-path thank-you">
        <i class="accepted-icon"></i>
        <h3><?php echo \TS_Functions::__('Thank You'); ?></h3>
        <p><?php echo \TS_Functions::__('You made a big contribution in progress of our job'); ?></p>
    </div>

<?php } else { ?>

    <div class="main-path lang-<?php echo $lang; ?>">
        <form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data" class="surveys">
            <div class="question-wrap">
                <?php echo $tree; ?>
            </div>

            <div class="page-nav">
                <?php if ($totalPagesCount > 1) { ?>
                    <div class="page-nav-content">
                        <?php echo \TS_Functions::__('Page'); ?> <div class="current"><?php echo $pageId; ?></div> of <?php echo $totalPagesCount; ?>
                    </div>
                <?php } ?>
                <div class="page-nav-arrows">
                    <?php if ($pageId > 1) { ?>
                        <a href="#" class="survey-submit back purple-button">
                        <span class="prev ">
                            <i>Back</i>
                        </span>
                        </a>
                    <?php } ?>

                    <?php if ($totalPagesCount > 1 && $pageId < $totalPagesCount) { ?>
                        <a href="#" class="survey-submit continue  purple-button">
                        <span class="next">
                                <i>Continue</i>
                        </span>
                        </a>
                    <?php } elseif ($pageId >= $totalPagesCount) { ?>
                        <a href="#" class="survey-submit submit btn btn-lg btn-green">
                            <span class="next"><?php echo \TS_Functions::__('Submit'); ?></span>
                        </a>
                    <?php } ?>
                </div>
            </div>

            <input type="hidden" id="pageAction" name="pageAction" value="">
            <div class="blocking-layer ajax-loader hidden"></div>
        </form>
    </div>
    <script>
        var GLOBAL = GLOBAL || {};
        GLOBAL.pageInfo = {
            id: 'survey',
            submitFailed: <?php echo (int) $haveValidationFails; ?>
        };
    </script>

<?php } ?>