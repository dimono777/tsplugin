<?php
namespace tradersoft\widgets;
use tradersoft\widgets\base\Widget;
use TS_Functions;
/**
 * Class TS_RSS_Widget
 *
 * @author Anatolii Lishchynskyi <anatolii.lishchynsky@tstechpro.com>
 */
class Rss extends Widget {

	/**
	 * @param array $args
	 * @param array $instance
	 */
    protected function _widget($args, $instance)
    {
	}

    protected function _loadInlineScripts()
    {
        if ( ! \TSInit::$app->request->isLocal) {
            $this->_mediaFiles
                ->addScriptInline(
                    $this->_render(
                        'js/rss',
                        [
                            'domain' => \TSInit::$app->request->getMainDomain(),
                            'crmId' => \TSInit::$app->trader->get('crmId'),
                            'currencyPrecision' => \tradersoft\helpers\Currency::getInstance()->getPrecision(),
                        ]
                    )
                );
        }
    }

	/**
	 * @return string
	 */
	protected function _getName() {
		return \TS_Functions::__( 'TraderSoft RSS' );
	}
}