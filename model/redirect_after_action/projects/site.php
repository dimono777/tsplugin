<?php

namespace tradersoft\model\redirect_after_action\projects;

use TSInit;
use tradersoft\model\redirect_after_action\abstracts\Projects as AbstractProjects;

class Site extends AbstractProjects
{
    const ID = 1;

    const TITLE = 'Site'; // Title for form

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

        foreach (get_pages() as $page) { // List of pages
            if ($page->post_status != 'publish') {
                continue;
            }

            $result[] = $this->_getLinkData(
                $page->ID,
                $page->post_title,
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
        return TSInit::$app->request->getLink("?page_id={$pageId}");
    }
}