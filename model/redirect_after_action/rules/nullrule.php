<?php

namespace tradersoft\model\redirect_after_action\rules;

use tradersoft\model\redirect_after_action\abstracts\Rules as AbstractRules;

/**
 * Default rule
 * It is intended for redirects that do not need combinations of rules
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 */
class NullRule extends AbstractRules
{
    /**
     * Get active rule
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return integer $result
     */
    public function getActiveRule()
    {
        return self::RULE_NULL;
    }
}