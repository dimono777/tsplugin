<?php
namespace tradersoft\widgets;
use tradersoft\widgets\base\Widget_Controller;

/**
 * Registration form widget.
 *
 * Use:
 * <?php the_widget('tradersoft\widgets\Registration'); ?>
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Registration extends Widget_Controller
{
    private $_shortCode = '[TS-REGISTRATION]';

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
        return \TS_Functions::__('TraderSoft Register Form');
    }
}