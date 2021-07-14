<?php

namespace tradersoft\model\redirect_after_action\projects;

use tradersoft\model\redirect_after_action\abstracts\Projects as AbstractProjects;
use tradersoft\helpers\Platform as PlatformHelper;

/**
 * Operations related to the platform
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 */
class Platform extends AbstractProjects
{
    const ID = 2;

    const TITLE = 'Platform'; // Title for form

    /**
     * Get page links
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param string $currentValue
     *
     * @return array
     */
    public function getPageLinks($currentValue = '')
    {
        $result = [];

        foreach (PlatformHelper::getPlatformURLsConfig() as $urlKey => $urlData) {
            if (!$urlData['showInList']) {
                continue;
            }

            $result[] = $this->_getLinkData(
                $urlKey,
                $urlData['title'],
                $currentValue
            );
        }

        return $result;
    }

    /**
     * Get page link
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param $pageId
     *
     * @return string
     */
    public function getPageLink($pageId)
    {
        return PlatformHelper::getURL($pageId);
    }
}