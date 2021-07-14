<?php

namespace tradersoft\settings\redirect_after_action;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Config;
use tradersoft\model\redirect_after_action\rule\Combination;

class Model
{
    /** @var string */
    private $_formSelectName = 'redirect_after_action';

    /** @var array */
    private $_actions = [];

    /**
     * Get form select name
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @return string
     */
    public function getFormSelectName()
    {
        return $this->_formSelectName;
    }

    /**
     * Get action objects
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @return array
     */
    public function getActions()
    {
        if ($this->_actions) {
            return $this->_actions;
        }

        /** @var array $actions */
        $actions = array_keys(
            Config::get([
                'redirect_after_action',
                'rules',
                'actions'
            ])
        );

        foreach ($actions as $action) {

            $class = "tradersoft\model\\redirect_after_action\actions\\$action";

            if (class_exists($class)) {
                $this->_actions[] = (new $class);
            }
        }

        return $this->_actions;
    }

    /**
     * Get combinations of rules for current action
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param array $actionRules
     *
     * @return array $result
     */
    public function getCombinationsOfRules(array $actionRules)
    {
        /** @var array $result */
        $result = [];

        foreach (Combination::generate($actionRules) as $combinationOfRules) {
            $result[] = [
                'name' => Combination::encode(array_keys($combinationOfRules)),
                'title' => implode(' + ', $combinationOfRules)
            ];
        }

        return $result;
    }

    /**
     * Get action rule form select name
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param string $actionName
     * @param string $ruleName
     *
     * @return string
     */
    public function getActionRuleFormSelectName($actionName, $ruleName)
    {
        return $this->_getActionRuleFormName($actionName, $ruleName, 'page');
    }

    /**
     * Get action rule custom form name
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param string $actionName
     * @param string $ruleName
     *
     * @return string
     */
    public function getActionRuleFormCustomName($actionName, $ruleName)
    {
        return $this->_getActionRuleFormName($actionName, $ruleName, 'custom');
    }

    /**
     * Get action rule form custom name
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param string $actionName
     * @param string $ruleName
     *
     * @return string
     */
    public function getActionRuleFormCustomId($actionName, $ruleName)
    {
        $ruleName = str_replace('.', '_', $ruleName);

        return $this->getFormSelectName() . "_{$actionName}_{$ruleName}";
    }

    /**
     * Select option selected
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param array $link
     *
     * @return string
     */
    public function optionSelected(array $link)
    {
        return (Arr::get($link, 'active')) ? ' selected="selected"' : '';
    }

    /**
     * Get action rule form name
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param int $actionId
     * @param string $ruleName
     * @param string $elementKey
     *
     * @return string
     */
    protected function _getActionRuleFormName($actionId, $ruleName, $elementKey)
    {
        return $this->getFormSelectName() . "[{$actionId}][{$ruleName}][{$elementKey}]";
    }
}