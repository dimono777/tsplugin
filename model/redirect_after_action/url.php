<?php

namespace tradersoft\model\redirect_after_action;

use tradersoft\model\redirect_after_action\rule\CombinationValues as RuleCombinationValues;
use tradersoft\model\redirect_after_action\projects\Site as ProjectSite;
use tradersoft\model\redirect_after_action\projects\Platform as ProjectPlatform;
use tradersoft\model\redirect_after_action\projects\Custom as ProjectCustom;

/**
 * Class Url
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 */
class Url
{
    /** @var RuleCombinationValues */
    private $_ruleCombinationValues;

    /**
     * @param RuleCombinationValues $combinationValues
     */
    public function __construct(RuleCombinationValues $combinationValues)
    {
        $this->_ruleCombinationValues = $combinationValues;
    }

    /**
     * Get redirect url after some action
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return string
     */
    public function get()
    {
        // Create link by rule combination values
        switch ($this->_ruleCombinationValues->getProject()) {
            case ProjectSite::ID:
                $result = (new ProjectSite())->getPageLink($this->_ruleCombinationValues->getPageId());
                break;

            case ProjectPlatform::ID:
                $result = (new ProjectPlatform())->getPageLink($this->_ruleCombinationValues->getPageId());
                break;

            case ProjectCustom::ID:
                $result = $this->_ruleCombinationValues->getCustom();
                break;

            default:
                $result = '';
                break;
        }

        return $result;
    }
}