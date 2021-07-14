<?php

namespace tradersoft\model\redirect_after_action\abstracts;

use tradersoft\model\redirect_after_action\interfaces\Projects as InterfaceProjects;
use tradersoft\model\redirect_after_action\settings\ValueEncoder as SettingsValueEncoder;

/**
 * Operations related to projects
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 */
abstract class Projects implements InterfaceProjects
{
    const ID = 0;

    const TITLE = ''; // Title for form

    /**
     * Get link data
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param integer $pageId
     * @param string $title
     * @param string $currentValue
     *
     * @return array
     */
    protected function _getLinkData($pageId, $title, $currentValue)
    {
        $value = SettingsValueEncoder::encode([
            static::ID,
            $pageId
        ]);

        return [
            'value' => $value,
            'title' => $title,
            'active' => ($currentValue == $value),
        ];
    }
}