<?php
namespace tradersoft\widgets;
use tradersoft\widgets\base\Widget_Controller;
/**
 * Verification upload form widget.
 *
 * Use:
 * <?php the_widget('TS_Verification_Upload_Widget'); ?>
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Verification_Upload extends Widget_Controller
{
    private $_shortCode = '[TS-ACCOUNT-VERIFICATION-UPLOAD]';

    public function rules()
    {
        return [
            'roles' => '@'
        ];
    }

    public function getShortCode()
    {
        return $this->_shortCode;
    }

    protected function _getName()
    {
        return \TS_Functions::__('TraderSoft Verification Upload Form');
    }
}