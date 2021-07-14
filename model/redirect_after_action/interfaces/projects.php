<?php

namespace tradersoft\model\redirect_after_action\interfaces;

/**
 * Interface Projects
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 */
interface Projects
{
    /**
     * Get page links
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param string $currentValue
     *
     * @return mixed
     */
    public function getPageLinks($currentValue = '');

    /**
     * Get page link
     *
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @param $pageId
     *
     * @return mixed
     */
    public function getPageLink($pageId);
}