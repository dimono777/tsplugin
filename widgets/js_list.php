<?php

namespace tradersoft\widgets;

use tradersoft\widgets\base\Widget,
    TS_Functions, TSInit;

/**
 * Class js list widget
 *
 * @author Alexander Shpak <alexander.shpak@tstechpro.com>
 */
class Js_List extends Widget {

    /**
     * @param array $args
     * @param array $instance
     *
     * @author Alexander Shpak <alexander.shpak@tstechpro.com>
     */
    protected function _widget($args, $instance)
    {

    }

    /**
     * @return string
     *
     * @author Alexander Shpak <alexander.shpak@tstechpro.com>
     */
    protected function _getName() {
        return \TS_Functions::__( 'TraderSoft Js List' );
    }
}