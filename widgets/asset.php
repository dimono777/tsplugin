<?php
namespace tradersoft\widgets;
use tradersoft\widgets\base\Widget_Controller;

/**
 * Asset widget.
 *
 * Use:
 * <?php the_widget('tradersoft\widgets\Asset'); ?>
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Asset extends Widget_Controller
{
    private $_shortCode = '[TS-ASSETS-INDEX]';

    public function getShortCode()
    {
        return $this->_shortCode;
    }

    protected function _getName()
    {
        return \TS_Functions::__('TraderSoft Asset Widget');
    }
}