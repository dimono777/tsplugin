<?php
namespace tradersoft\widgets;
use tradersoft\widgets\base\Widget;

/**
 * Registration mini form widget.
 *
 * instance properties:
 *      // for button
 *      'authLinkText'
 *      'regLinkText'
 *      'showForm'
 *      // for form:
 *      'ajaxEnable' => bool;
 *      'template'   => string; path to template
 *      'js'         => string; path to js file
 *      'css'        => string; path to css file
 *
 * Use:
 * <?php the_widget('tradersoft\widgets\Welcome'); ?>
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Welcome extends Widget
{
    private $_instanceButton = [];

    protected function _widget($args, $instance)
    {
        $this->prepareWidget($args, $instance);
        if (\TSInit::$app->trader->isGuest) {
            the_widget(Welcome_Button::class, $this->_instanceButton);
        } else {
            the_widget(Welcome_Trader::class);
        }
    }

    /**
     * Prepare widget args
     * @param $args array
     * @param $instance array
     */
    public function prepareWidget($args, $instance)
    {
        if(!empty($instance['authLinkText'])) {
            $this->_instanceButton['authLinkText'] = $instance['authLinkText'];
            unset($instance['authLinkText']);
        }
        if(!empty($instance['regLinkText'])) {
            $this->_instanceButton['regLinkText'] = $instance['regLinkText'];
            unset($instance['regLinkText']);
        }
        if (!empty($instance['regLinkAttr'])) {
            $this->_instanceButton['regLinkAttr'] = (array)$instance['regLinkAttr'];
            unset($instance['regLinkAttr']);
        }
        if(isset($instance['showForm'])) {
            $this->_instanceButton['showForm'] = $instance['showForm'];
            unset($instance['showForm']);
        }
        if(isset($instance['ajaxEnable'])) {
            $this->_instanceButton['ajaxEnable'] = $instance['ajaxEnable'];
            unset($instance['ajaxEnable']);
        }
        if(isset($instance['template'])) {
            $this->_instanceButton['template'] = $instance['template'];
            unset($instance['template']);
        }
        if(isset($instance['js'])) {
            $this->_instanceButton['js'] = $instance['js'];
            unset($instance['js']);
        }
        if(isset($instance['css'])) {
            $this->_instanceButton['css'] = $instance['css'];
            unset($instance['css']);
        }
    }

    protected function _getName()
    {
        return \TS_Functions::__('TraderSoft Welcome Widget');
    }
}