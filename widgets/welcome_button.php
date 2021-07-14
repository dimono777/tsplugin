<?php
namespace tradersoft\widgets;

use tradersoft\helpers\Html;

/**
 * Registration button widget.
 *
 * instance properties:
 *      'authLinkText'
 *      'regLinkText'
 *      'regLinkAttr'
 *      'showForm'
 *      // with form extra options:
 *      'ajaxEnable' => bool;
 *      'template'   => string; path to template
 *      'js'         => string; path to js file
 *      'css'        => string; path to css file
 *
 * Use:
 * <?php the_widget('tradersoft\widgets\Welcome_Button'); ?>
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Welcome_Button extends Welcome_Form
{
    protected $_authLinkText    = 'Log In';
    protected $_regLinkText     = 'Register';
    protected $_showForm        = true;
    protected $_regLinkAttr     = [];


    protected function _widget($args, $instance)
    {
        $this->prepareWidget($args, $instance);
        if (\TSInit::$app->trader->isGuest) {
            echo $this->_render('index', ['authLinkText' => $this->_authLinkText, 'regLink' => $this->_getRegLink()]);
            if($this->_showForm) {
                the_widget(Welcome_Form::class, $instance, $args);
            }
        }
    }

    /**
     * @return string
     */
    protected function _getRegLink()
    {
        if (empty($this->_regLinkAttr['text'])) {
            $this->_regLinkAttr['text'] = $this->_regLinkText;
        }
        if (empty($this->_regLinkAttr['key'])) {
            $this->_regLinkAttr['key'] = 'TS-REGISTRATION';
        }
        if (empty($this->_regLinkAttr['html-class'])) {
            $this->_regLinkAttr['html-class'] = 'ts-btn ts-btn-open-acc';
        }

        return Html::getFormLink($this->_regLinkAttr);
    }

    /**
     * Prepare widget args
     * @param $args array
     * @param $instance array
     */
    public function prepareWidget($args, $instance)
    {
        if(!empty($instance['authLinkText'])) {
            $this->_authLinkText = $instance['authLinkText'];
            unset($instance['authLinkText']);
        }
        if(!empty($instance['regLinkText'])) {
            $this->_regLinkText = $instance['regLinkText'];
            unset($instance['regLinkText']);
        }
        if (!empty($instance['regLinkAttr'])) {
            $this->_regLinkAttr = (array)$instance['regLinkAttr'];
        }
        if(isset($instance['showForm'])) {
            $this->_showForm = (bool)$instance['showForm'];
            unset($instance['showForm']);
        }
    }

    protected function _getName()
    {
        return \TS_Functions::__('TraderSoft Welcome Button Widget');
    }
}