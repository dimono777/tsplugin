<?php
namespace tradersoft\widgets;
use tradersoft\widgets\base\Widget_Controller;

/**
 * Partner registration form widget.
 *
 * Use:
 * <?php the_widget('tradersoft\widgets\Partner_Registration'); ?>
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Partner_Registration extends Widget_Controller
{
    private $_shortCode = '[TS-PARTNERS-REGISTRATION]';

    public function getShortCode()
    {
        return $this->_shortCode;
    }

    protected function _getName()
    {
        return \TS_Functions::__('TraderSoft Partner Registration Form');
    }
}