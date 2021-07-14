<?php
namespace tradersoft\widgets;
use tradersoft\widgets\base\Widget;
use tradersoft\widgets\base\TemplateWithFormRendering;

/**
 * Registration form widget.
 *
 * $instance properties:
 *      'ajaxEnable' => bool;
 *      'template'   => string; path to template
 *      'js'         => string; path to js file
 *      'css'        => string; path to css file
 *
 * Use:
 * <?php the_widget('tradersoft\widgets\Welcome_Form'); ?>
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Welcome_Form extends Widget
{
    use TemplateWithFormRendering;

    protected $_formSetting     = [
        'action'        => '/trader/login',
        'htmlOptions'   => ['id'=>'ts-hello-auth-form'],
        'ajaxEnable'    => true,
        'ajaxUrl'       => '/trader/login',
        'ajaxCallback'  => 'callBackLogin'
    ];

    public function formField()
    {
        return [
            '[email]' => [
                'field' => 'email',
                'fieldOptions' => [],
                'input' => 'textInput',
                'inputOptions' =>['id' => 'auth_email', 'class' => 'form-input form-free'],
            ],
            '[password]' => [
                'field' => 'password',
                'input' => 'passwordInput',
                'inputOptions' => ['id'=> 'auth_password', 'class' => 'form-input form-free']
            ],
            '[checkbox]' =>[
                'field' => 'rememberMe',
                'input' => 'checkbox',
                'inputOptions' => ['id '=> 'remember-me', 'class' => 'form-input form-free']
            ],
            '[button]' => [
                'label' => \TS_Functions::__('Sign up'),
                'options' => ['class'=>'btn btn-orange btn-medium']
            ]
        ];
    }

    /**
     * @return \tradersoft\model\Model
     */
    public function getModel()
    {
        return new \tradersoft\model\Trader_Login();
    }

    protected function _widget($args, $instance)
    {
        $this->prepareWidget($args, $instance);
        if (\TSInit::$app->trader->isGuest) {
            $this->_renderTemplate();
        }
    }

    /**
     * instance properties:
     *      'ajaxEnable' => bool;
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
        if(isset($instance['ajaxEnable'])) {
            $this->_formSetting['ajaxEnable'] = $instance['ajaxEnable'];
        }
        $this->_formOptions = $this->_formSetting;
        $this->templateModelName = 'modelTraderLogin';
        $this->_prepareArgs($args, $instance);
    }

    protected function _getName()
    {
        return \TS_Functions::__('TraderSoft Welcome Form Widget');
    }
}