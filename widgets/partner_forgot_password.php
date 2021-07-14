<?php
namespace tradersoft\widgets;
use tradersoft\widgets\base\Widget_Controller;

/**
 * Partner forgot password form widget.
 *
 * Use:
 * <?php the_widget('tradersoft\widgets\Partner_Forgot_Password'); ?>
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Partner_Forgot_Password extends Widget_Controller
{
    private $_shortCode = '[TS-PARTNERS-FORGOT-PASSWORD]';

    public function getShortCode()
    {
        return $this->_shortCode;
    }

    protected function _getName()
    {
        return \TS_Functions::__('TraderSoft Partner Forgot Password Form');
    }
}