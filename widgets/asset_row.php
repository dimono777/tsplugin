<?php

namespace tradersoft\widgets;

use tradersoft\widgets\base\Widget, TSInit;

/**
 * Class Asset_Row - widget for showing assets running row
 * @package tradersoft\widgets
 *
 * @author Alexander Shpak <alexander.shpak@tstechpro.com>
 */
class Asset_Row extends Widget {

    /**
     * @inheritdoc
     */
    protected function _widget($args, $instance)
    {
        echo $this->_render( 'assets/row', [
            'domain' => TSInit::$app->request->getMainDomain(),
            'assetsIds' => (isset($instance['assetsIds'])) ? $instance['assetsIds'] : [],
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function _getName() {
        return \TS_Functions::__('TraderSoft - Assets running row widget');
    }
}