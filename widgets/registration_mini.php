<?php
namespace tradersoft\widgets;
use tradersoft\widgets\base\Widget_Controller;

/**
 * Registration mini form widget.
 *
 * Use:
 * <?php the_widget('tradersoft\widgets\Registration_Mini'); ?>
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Registration_Mini extends Widget_Controller
{
    private $_shortCode = '[TS-REGISTRATION-MINI]';

    public function rules()
    {
        return [
           'roles' => '?'
        ];
    }

    public function getShortCode()
    {
        return $this->_shortCode;
    }

    protected function _getName()
    {
        return \TS_Functions::__('TraderSoft Registration Mini Form');
    }
}