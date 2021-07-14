<?php
namespace tradersoft\widgets;
use tradersoft\helpers\HeaderSetting;
use tradersoft\widgets\base\Widget;
use tradersoft\widgets\base\TemplateRendering;

/**
 * Registration trader widget.
 *
 * Use:
 * <?php the_widget('tradersoft\widgets\Welcome_Trader'); ?>
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Welcome_Trader extends Widget
{
    use TemplateRendering;

    public function templateFields()
    {
        return [
            '[balance]' => '_getFieldBalance',
            '[finance-info-type-label]' => '_getFinanceInfoTypeLabel',
            '[finance-info-type-value]' => '_getFinanceInfoTypeValue',
            '[trader-crm-id]' => '_getFieldTraderCrmId',
            '[currency-symbol]' => '_getFieldTraderCurrencySymbol',
        ];
    }

    protected function _widget($args, $instance)
    {
        $this->prepareWidget($args, $instance);
        if (!\TSInit::$app->trader->isGuest) {
            $this->_renderTemplate();
        }
    }

    protected function _getName()
    {
        return \TS_Functions::__('TraderSoft Welcome Trader Widget');
    }

    /**
     * instance properties:
     *      'template'   => string; path to template
     *      'js'         => string; path to js file
     *      'css'        => string; path to css file
     *
     * Prepare widget args
     * @param $args array
     * @param $instance array
     */
    public function prepareWidget($args, $instance)
    {
        $this->_prepareArgs($args, $instance);
    }

    /**
     * @return string
     * @deprecated use _getFinanceInfoTypeValue
     */
    protected function _getFieldBalance()
    {
        /** @var $trader \tradersoft\model\Trader */
        $trader = \TSInit::$app->trader;
        if ($trader->isGuest) {
            return '0.00';
        }

        return '<?php $trader = \TSInit::$app->trader; echo $trader->getBalance();?>';
    }

    /**
     * @return string
     */
    protected function _getFinanceInfoTypeValue()
    {
        $trader = \TSInit::$app->trader;

        if ($trader->isGuest) {
            return '0.00';
        }

        return '<?php $trader = \TSInit::$app->trader; echo $trader->getFinanceInfoTypeValue();?>';
    }

    /**
     * @return string
     */
    protected function _getFinanceInfoTypeLabel()
    {
        return '<?php echo \tradersoft\helpers\HeaderSetting::getCurrentFinanceInfoTypeLabel();?>';
    }

    /**
     * @return string
     */
    protected function _getFieldTraderCurrencySymbol()
    {
        /** @var $trader \tradersoft\model\Trader */
        $trader = \TSInit::$app->trader;
        if ($trader->isGuest) {
            return '';
        }

        return '<?php echo \tradersoft\helpers\Currency::getInstance()->getSymbol(); ?>';
    }

    /**
     * @return string
     */
    public function _getFieldTraderCrmId()
    {
        /** @var $trader \tradersoft\model\Trader */
        $trader = \TSInit::$app->trader;
        if ($trader->isGuest) {
            return '';
        }

        return '<?php $trader = \TSInit::$app->trader; echo $trader->get(\'crmId\', \'\');?>';
    }
}