<?php

use tradersoft\model\redirect_after_action\abstracts\Rules;
use tradersoft\model\redirect_after_action\actions\Authorization;
use tradersoft\model\redirect_after_action\actions\Registration;
use tradersoft\model\redirect_after_action\actions\Survey;

return [
    'rules' => [
        'titles' => [
            Rules::RULE_NULL => '',
            Rules::RULE_DEPOSITORS => 'Depositors',
            Rules::RULE_NON_DEPOSITORS => 'Non depositors',
            Rules::RULE_TERMS_AND_CONDITIONS_ACCEPTED => 'Terms and conditions accepted',
            Rules::RULE_TERMS_AND_CONDITIONS_NOT_ACCEPT => 'Terms and conditions not accept',
            Rules::RULE_SURVEY_SUBMIT => 'Survey submitted',
        ],
        'actions' => [
            Registration::NAME => [
                Rules::GROUP_TERMS_AND_CONDITIONS => [
                    Rules::RULE_TERMS_AND_CONDITIONS_ACCEPTED,
                    Rules::RULE_TERMS_AND_CONDITIONS_NOT_ACCEPT,
                ],
            ],
            Authorization::NAME => [
                Rules::GROUP_DEPOSIT => [
                    Rules::RULE_DEPOSITORS,
                    Rules::RULE_NON_DEPOSITORS,
                ],
            ],
            Survey::NAME => [
                Rules::GROUP_SURVEY => [
                    Rules::RULE_SURVEY_SUBMIT
                ],
            ],
        ],
    ],
];