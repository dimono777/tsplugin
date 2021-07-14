<?php
namespace tradersoft\widgets;
use tradersoft\widgets\base\Widget_Controller;

/**
 * Trader forgot password form widget.
 *
 * Use:
 * <?php the_widget('tradersoft\widgets\Change_Password'); ?>
 *
 * @author Bogdan Medvedev <bogdan.medvedev@tstechpro.com>
 */
class Change_Password extends Widget_Controller
{
    private $_shortCode = '[TS-CHANGE-PASSWORD]';

    public function getShortCode()
    {
        return $this->_shortCode;
    }

    protected function _getName()
    {
        return \TS_Functions::__('TraderSoft Trader Change Password Form');
    }
}