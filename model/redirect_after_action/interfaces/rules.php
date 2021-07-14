<?php

namespace tradersoft\model\redirect_after_action\interfaces;

/**
 * Class Rules
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 */
interface Rules
{
    /**
     * Get active rule
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return int
     */
    public function getActiveRule();
}