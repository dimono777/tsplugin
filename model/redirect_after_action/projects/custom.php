<?php

namespace tradersoft\model\redirect_after_action\projects;

use tradersoft\model\redirect_after_action\abstracts\Projects as AbstractProjects;

class Custom extends AbstractProjects
{
    const ID = 3;

    const TITLE = 'Custom'; // Title for form

    const OPTION_CUSTOM_FIELD = 1;

    /**
     * Get page links
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param string $currentValue
     *
     * @return array $result
     */
    public function getPageLinks($currentValue = '')
    {
        $result = [];

        /** @var array */
        $options = [
            self::OPTION_CUSTOM_FIELD => \TS_Functions::__('Custom field'),
        ];

        foreach ($options as $id => $title) { // List of pages
            $result[] = $this->_getLinkData(
                $id,
                $title,
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
        return '';
    }
}