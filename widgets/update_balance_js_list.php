<?php

namespace tradersoft\widgets;

use tradersoft\helpers\Currency,
    tradersoft\widgets\base\Widget,
    TS_Functions, TSInit,
    tradersoft\helpers\HeaderSetting;

/**
 * Widget to require exact js files for updating balance
 *
 * @author Alexander Shpak <alexander.shpak@tstechpro.com>
 */
class Update_Balance_Js_List extends Widget {

    /**
     * @param array $args
     * @param array $instance
     *
     * @author Alexander Shpak <alexander.shpak@tstechpro.com>
     */
    protected function _widget($args, $instance)
    {

    }

    protected function _loadInlineScripts()
    {
        $this->_mediaFiles
            ->addScriptInline(
                $this->_render(
                    'js/update_balance_js_list',
                    [
                        'crmHashId' => TSInit::$app->trader->get('crmHashId'),
                        'currencyPrecision' => Currency::getInstance()->getPrecision(),
                        'currentFinanceInfoTypeId' => HeaderSetting::getCurrentFinanceInfoTypeId(),
                    ]
                )
            );
    }

    /**
     * @return string
     *
     * @author Alexander Shpak <alexander.shpak@tstechpro.com>
     */
    protected function _getName() {
        return \TS_Functions::__('TraderSoft - Update balance js list');
    }
}
