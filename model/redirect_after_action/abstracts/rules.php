<?php

namespace tradersoft\model\redirect_after_action\abstracts;

use tradersoft\model\redirect_after_action\interfaces\Rules as InterfaceRules;

/**
 * Class Rules
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 */
abstract class Rules implements InterfaceRules
{
    // Groups
    const GROUP_NULL = 1;
    const GROUP_DEPOSIT = 2;
    const GROUP_TERMS_AND_CONDITIONS = 3;
    const GROUP_SURVEY = 4;

    // Rules
    const RULE_NULL = 1;
    const RULE_DEPOSITORS = 2;
    const RULE_NON_DEPOSITORS = 3;
    const RULE_TERMS_AND_CONDITIONS_ACCEPTED = 4;
    const RULE_TERMS_AND_CONDITIONS_NOT_ACCEPT = 5;
    const RULE_SURVEY_SUBMIT = 6;
}