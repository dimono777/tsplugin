<?php

namespace tradersoft\model\redirect_after_action\rules;

use tradersoft\model\redirect_after_action\abstracts\Rules as AbstractRules;

/**
 * Class Survey
 *
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class Survey extends AbstractRules
{
    /**
     * Get active rule
     */
    public function getActiveRule()
    {

        return self::RULE_SURVEY_SUBMIT;
    }
}