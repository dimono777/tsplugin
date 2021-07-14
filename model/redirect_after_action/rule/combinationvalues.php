<?php

namespace tradersoft\model\redirect_after_action\rule;

use tradersoft\helpers\Arr;
use tradersoft\model\redirect_after_action\abstracts\Actions as AbstractActions;
use tradersoft\model\redirect_after_action\abstracts\Rules as AbstractRules;
use tradersoft\model\redirect_after_action\rules\NullRule;
use tradersoft\model\redirect_after_action\rules\Deposit as RuleDeposit;
use tradersoft\model\redirect_after_action\rules\survey as RuleSurvey;
use tradersoft\model\redirect_after_action\rules\TermsAndConditions as RuleTermsAndConditions;
use tradersoft\model\redirect_after_action\rule\Combination as RuleCombination;

/**
 * Class CombinationValues
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 */
class CombinationValues
{
    /** @var AbstractActions */
    private $_action;

    /** @var string - Rule combination value */
    private $_projectId = 0;

    /** @var int - Rule combination value */
    private $_pageId = 0;

    /** @var int - Rule combination value */
    private $_custom = '';

    /**
     * @param AbstractActions $action
     */
    public function __construct(AbstractActions $action)
    {
        $this->_action = $action;

        $this->_setRuleCombinationValues();
    }

    /**
     * Get rule combination value - project
     * @return string
     */
    public function getProject()
    {
        return $this->_projectId;
    }

    /**
     * Get rule combination value - pageId
     * @return int
     */
    public function getPageId()
    {
        return $this->_pageId;
    }    
    
    /**
     * Get rule combination value - custom field
     * @return int
     */
    public function getCustom()
    {
        return $this->_custom;
    }

    /**
     * Set rule combination values: project, pageId
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     */
    private function _setRuleCombinationValues()
    {
        /** @var array */
        $activeRuleCombinationValues = $this->_getActiveRuleCombinationValues();

        $this->_projectId = Arr::get($activeRuleCombinationValues, 'project', '');
        $this->_pageId = Arr::get($activeRuleCombinationValues, 'page', '');
        $this->_custom = Arr::get($activeRuleCombinationValues, 'custom', '');
    }

    /**
     * Get active rule combination values
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return array
     */
    private function _getActiveRuleCombinationValues()
    {
        return Arr::get(
            $this->_action->getRuleCombinations(),
            $this->_getActiveRuleStringCombination(),
	        []
        );
    }

    /**
     * Get active rule string combination
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return string
     */
    private function _getActiveRuleStringCombination()
    {
        return RuleCombination::encode(
            $this->_getActiveRuleCombination()
        );
    }

    /**
     * Get the active rule combination
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return array
     */
    private function _getActiveRuleCombination()
    {
        /** @var array $combinationOfRules */
        $combinationOfRules = [];

        // Determine the active combination of rules
        foreach ($this->_action->getRules() as $group => $rules) {
            // Determine the active rule of the trader of a current group
            switch ($group) {
                case AbstractRules::GROUP_NULL:
                    $combinationOfRules[] = (new NullRule())->getActiveRule();
                    break;
                case AbstractRules::GROUP_DEPOSIT:
                    $combinationOfRules[] = (new RuleDeposit())->getActiveRule();
                    break;
                case AbstractRules::GROUP_TERMS_AND_CONDITIONS:
                    $combinationOfRules[] = (new RuleTermsAndConditions())->getActiveRule();
                    break;
                case AbstractRules::GROUP_SURVEY:
                    $combinationOfRules[] = (new RuleSurvey())->getActiveRule();
                    break;
                default:
                    break;
            }
        }

        return $combinationOfRules;
    }
}