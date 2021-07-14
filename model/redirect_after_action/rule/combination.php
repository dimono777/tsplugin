<?php

namespace tradersoft\model\redirect_after_action\rule;

use tradersoft\helpers\Config;

class Combination
{
    /** @var string */
    private static $_delimiter = '.';

    /**
     * Get combination of rules
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param array $ruleGroups
     * @param array $sequence
     * @return array $result
     */
    public static function generate(array $ruleGroups, array $sequence = [])
    {
        /** @var array $result */
        $result = [];

        foreach ($ruleGroups as $ruleGroupName => $rules) {
            unset($ruleGroups[$ruleGroupName]);

            foreach ($rules as $rule) {
                /** @var array $newSequence */
                $newSequence = $sequence + [$rule => self::_getRuleTitle($rule)];

                if ($ruleGroups) {
                    $result = array_merge($result, self::generate($ruleGroups, $newSequence));
                } else {
                    $result[] = $newSequence;
                }
            }

            break;
        }

        return $result;
    }

    /**
     * Encode combination rules
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param array $combinationRules
     * @return string
     */
    public static function encode(array $combinationRules)
    {
        return implode(self::$_delimiter, $combinationRules);
    }

    /**
     * Get rule title
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param integer $rule
     * @return array
     */
    private static function _getRuleTitle($rule)
    {
        return Config::get("redirect_after_action.rules.titles.{$rule}");
    }
}