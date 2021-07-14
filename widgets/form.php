<?php
namespace tradersoft\widgets;

use Exception;
use tradersoft\controllers\interfaces\IControllerWithModel;
use tradersoft\helpers\Arr;
use tradersoft\helpers\Config;
use tradersoft\helpers\system\PageKey;
use tradersoft\widgets\base\Widget_Controller;
use tradersoft\controllers\Base_Controller;

/**
 * Form widget.
 *
 * Use:
 * <?php the_widget('tradersoft\widgets\Form', ['shortCodeParams' => ['type'=>'Registration']]); ?>
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Form extends Widget_Controller
{
    public function rules()
    {
        $formType = Arr::get($this->_getShortCodeParams(), 'type');
        return Config::get("widget_access_rules.$formType", []);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getShortCode()
    {
        return PageKey::getPageShortCode(PageKey::KEY_FORMS);
    }

    protected function _getName()
    {
        return \TS_Functions::__('TraderSoft Form Widget');
    }

    /**
     * @param Base_Controller $controller
     * @return string
     * @throws Exception
     */
    protected function _getRenderingContent(Base_Controller $controller)
    {
        if (!($controller instanceof IControllerWithModel)) {
            throw new Exception('Invalid controller type');
        }

        return $controller->getModel()->getFormContent();
    }
}