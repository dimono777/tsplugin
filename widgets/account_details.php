<?php
namespace tradersoft\widgets;
use tradersoft\widgets\base\Widget_Controller;

/**
 * Partner authorization form widget.
 *
 * Use:
 * <?php the_widget('tradersoft\widgets\Account_Details'); ?>
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Account_Details extends Widget_Controller
{
    private $_shortCode = '[TS-ACCOUNT-DETAILS]';

    public function getShortCode()
    {
        return $this->_shortCode;
    }

    protected function _getName()
    {
        return \TS_Functions::__('TraderSoft Account Details Form');
    }
}