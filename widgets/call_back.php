<?php
namespace tradersoft\widgets;
use tradersoft\widgets\base\Widget_Controller;

/**
 * Call back form widget.
 *
 * Use:
 * <?php the_widget('tradersoft\widgets\Call_Back'); ?>
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Call_Back extends Widget_Controller
{
    protected $_formSetting     = [
        'htmlOptions'   => ['id'=>'ts-call-back-form'],
        'ajaxEnable'    => false,
        'ajaxUrl'       => '/home/call-back',
        'ajaxCallback'  => 'callBackCallBack'
    ];
    private $_shortCode = '[TS-CALL-BACK]';

    public function getShortCode()
    {
        return $this->_shortCode;
    }

    /**
     * instance properties:
     *      'ajaxEnable' => bool;
     *      'jsFile' => string;
     *      'ajaxCallback' => string; valid js function
     */
    protected function _widget($args, $instance)
    {
        if (isset($instance['ajaxEnable'])) {
            $this->_formSetting['ajaxEnable'] = $instance['ajaxEnable'];
        }
        if (isset($instance['ajaxCallback'])) {
            $this->_formSetting['ajaxCallback'] = $instance['ajaxCallback'];
        }
        if (isset($instance['jsFile'])) {
            $this->_addScript($instance['jsFile']);
        }
        \TSInit::$app->setVar('callBackFormOptions', $this->_formSetting);

        parent::_widget($args, $instance);
    }

    protected function _getName()
    {
        return \TS_Functions::__('TraderSoft Call Beck Form');
    }
}