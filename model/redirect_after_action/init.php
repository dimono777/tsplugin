<?php

namespace tradersoft\model\redirect_after_action;

use tradersoft\model\redirect_after_action\rule\CombinationValues as RuleCombinationValues;
use tradersoft\model\redirect_after_action\abstracts\Actions as AbstractActions;
use tradersoft\model\redirect_after_action\actions\Authorization as RedirectAfterAuthorization;
use tradersoft\model\redirect_after_action\actions\Registration as RedirectAfterRegistration;
use tradersoft\model\redirect_after_action\actions\Survey as RedirectAfterSurvey;

/**
 * Class Init
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 */
class Init
{
    /** @var AbstractActions */
    protected $action;

    /**
     * Init constructor.
     *
     * @param int $actionId
     */
    public function __construct($actionId)
    {
        switch ($actionId) {
            case RedirectAfterAuthorization::ID:
                $this->action = new RedirectAfterAuthorization();
                break;

            case RedirectAfterSurvey::ID:
                $this->action = new RedirectAfterSurvey();
                break;

            case RedirectAfterRegistration::ID:
            default:
                $this->action = new RedirectAfterRegistration();
                break;
        }
    }

    /**
     * Get redirect url after action
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return string
     */
    public function getUrl()
    {
        $ruleCombinationValues = new RuleCombinationValues($this->action);
        return (new Url($ruleCombinationValues))->get();
    }
}