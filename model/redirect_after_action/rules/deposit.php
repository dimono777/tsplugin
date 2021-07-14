<?php

namespace tradersoft\model\redirect_after_action\rules;

use tradersoft\model\redirect_after_action\abstracts\Rules as AbstractRules;
use TSInit;

/**
 * Class Deposit
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 */
class Deposit extends AbstractRules
{
    /**
     * Get active rule
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return integer $result
     */
    public function getActiveRule()
    {
        /** @var int $result */
        $result = 0;

        if (is_bool($hasDepositOrCashBack = TSInit::$app->trader->get('hasDepositOrCashback'))) {
            if ($hasDepositOrCashBack) {
                $result = self::RULE_DEPOSITORS;
            } else {
                $result = self::RULE_NON_DEPOSITORS;
            }
        }

        return $result;
    }
}