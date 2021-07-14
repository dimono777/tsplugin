<?php
namespace tradersoft\widgets;
use tradersoft\widgets\base\Widget_Controller;

/**
 * Withdrawal request form widget.
 *
 * Use:
 * <?php the_widget('tradersoft\widgets\Withdrawal_Request'); ?>
 *
 * @author Bogdan Medvedev <bogdan.medvedev@tstechpro.com>
 */
class Withdrawal_Request extends Widget_Controller
{
    private $_shortCode = '[TS-WITHDRAWAL]';

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
        return \TS_Functions::__('TraderSoft Withdrawal Request Form');
    }
}