<?php
namespace tradersoft\widgets;
use tradersoft\widgets\base\Widget_Controller;
/**
 * Mini asset widget.
 *
 * Use:
 * <?php the_widget('tradersoft\widgets\Asset'); ?>
 *
 * @author Alexandr Tomenko <dmitriy.kachurovskiy@tstechpro.com>
 */
class Asset_Mini extends Widget_Controller
{
    private $_shortCode = '[TS-ASSETS-MINI]';

    public function getShortCode()
    {
        return $this->_shortCode;
    }

    protected function _getName()
    {
        return \TS_Functions::__('TraderSoft Asset Mini Widget');
    }
}