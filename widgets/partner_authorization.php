<?php
namespace tradersoft\widgets;
use tradersoft\widgets\base\Widget_Controller;

/**
 * Partner authorization form widget.
 *
 * Use:
 * <?php the_widget('tradersoft\widgets\Partner_Authorization'); ?>
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Partner_Authorization extends Widget_Controller
{
    private $_shortCode = '[TS-PARTNERS-AUTHORIZATION]';

    public function getShortCode()
    {
        return $this->_shortCode;
    }

    protected function _getName()
    {
        return \TS_Functions::__('TraderSoft Partner Authorization Form');
    }
}