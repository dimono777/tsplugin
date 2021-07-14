<?php

namespace tradersoft\model\redirect_after_action\rules;

use TSInit;
use tradersoft\model\Trader as ModelTrader;
use tradersoft\model\redirect_after_action\abstracts\Rules as AbstractRules;

/**
 * Class TermsAndConditions
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 */
class TermsAndConditions extends AbstractRules
{
    /**
     * Get active rule
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return integer $result
     */
    public function getActiveRule()
    {
        /** @var int $termStatus */
        $termStatus = TSInit::$app->trader->get('termStatus', ModelTrader::TERMS_STATUS_NOT_SEEN);

        return ($termStatus == ModelTrader::TERMS_STATUS_ACCEPTED)
            ? self::RULE_TERMS_AND_CONDITIONS_ACCEPTED
            : self::RULE_TERMS_AND_CONDITIONS_NOT_ACCEPT;
    }
}