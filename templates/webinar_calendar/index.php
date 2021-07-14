<?php

/**
 * @var array $currentTitles
 * @var tradersoft\helpers\Form $form
 * @var array $monthsForSelect
 * @var string $registerLink
 * @var array $months
 * @var array $days
 */

$webinarCalendarData = TSInit::$app->getVar('webinarCalendarData', []);
extract($webinarCalendarData);

?>
<section class="account-tabs-sidebar-section gradient-section">
    <div class="section-inner">
        <div class="row">
            <div class="calendar col-lg-12">
                <div class="calendar-content">
                    <div class="calendar-title">
                        <div class="current-month">
                            <?php echo implode(" - ", $currentTitles); ?>
                        </div>
                        <div class="change-month">
                            <span class="prev nav-btn glyphicons glyphicons-chevron-left"></span>
                            <div class="select-month">
                            <?php

                                echo $form->field('month')
                                    ->dropDownList(
                                        $monthsForSelect,
                                        [
                                            'class' => 'month-select',
                                        ]
                                    );
                            ?>
                            </div>
                            <span class="next nav-btn glyphicons glyphicons-chevron-right"></span>
                        </div>
                    </div>
                    <div class="calendar-body">
                        <div class="calendar-wrap">
                            <div class="prev change-week"><i></i><span><?php echo \TS_Functions::__("Prev </br>week"); ?></span></div>
                            <div class="week-days"></div>
                            <div class="next change-week"><span><?php echo \TS_Functions::__("Next </br>week"); ?></span><i></i></div>
                        </div>
                        <h2><?php echo \TS_Functions::__("Webinar details"); ?></h2>
                        <div class="calendar-day-events"></div>
                    </div>
                </div>
                <div class="js-overlay"><div class="js-spinner"></div></div>
            </div>
        </div>
    </div>
</section>

<script>

    var GLOBAL = GLOBAL || {};

    GLOBAL.language = '<?php echo \TS_Functions::getCurrentLanguage(); ?>';
    GLOBAL.webinarsTranslations = {
        "start": "<?php echo \TS_Functions::__("Start"); ?>",
        "end": "<?php echo \TS_Functions::__("End"); ?>",
        "join": "<?php echo \TS_Functions::__("Join now"); ?>",
        "joinSuccessfulText": "<?php echo \TS_Functions::__("Your registration was successful!"); ?>",
        "joinDeniedText": "<?php echo \TS_Functions::__("Sorry, you do not have access to this webinar."); ?>",
        "joinNotEligibleText": "<?php
            echo \TS_Functions::__("Thank you for taking interest in "
                . "this Webinar, however at this time your account is not yet eligible "
                . "to enter. Please contact your Broker for further details.");
            ?>",
        "registerText": "<?php echo \TS_Functions::__("In order to join the webinar please log in."); ?>",
        "registerLink": "<?php echo $registerLink; ?>",
        "requestSentText": "<?php echo \TS_Functions::__("Your request to join the webinar has  been sent. We will contact you as soon as possible."); ?>",
        "register": "<?php echo \TS_Functions::__("Create Account"); ?>",
        "link": "<?php echo \TS_Functions::__("Open the webinar"); ?>",
        "yourTime": "<?php echo \TS_Functions::__("your time"); ?>",
        "UTC": "<?php echo \TS_Functions::__("UTC"); ?>",
        "months": ["<?php echo implode('","', $months); ?>"],
        "days": ["<?php echo implode('","', $days); ?>"]
    };
</script>