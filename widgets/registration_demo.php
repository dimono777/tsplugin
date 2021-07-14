<?php
namespace tradersoft\widgets;
use tradersoft\widgets\base\Widget_Controller;

/**
 * Registration demo form widget.
 *
 * Use:
 * <?php the_widget('tradersoft\widgets\Registration_Demo'); ?>
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Registration_Demo extends Widget_Controller
{
    private $_shortCode = '[TS-REGISTRATION-DEMO]';

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
        return \TS_Functions::__('TraderSoft Registration Demo Form');
    }
}