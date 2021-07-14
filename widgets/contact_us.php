<?php
namespace tradersoft\widgets;
use tradersoft\widgets\base\Widget;
use tradersoft\widgets\base\Widget_Controller;
use tradersoft\widgets\base\TemplateWithFormRendering;

/**
 * Contact us form widget.
 *
 * $instance properties:
 *      'template'   => string; path to template
 *
 * Use:
 * <?php the_widget('tradersoft\widgets\Contact_Us'); ?>
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Contact_Us extends Widget_Controller
{
    use TemplateWithFormRendering;

    public function getShortCode()
    {
        return '[TS-CONTACT-US]';
    }

    public function formField()
    {
        return [
            '[full-name]' => [
                'field' => 'fullName',
                'input' => 'textInput'
            ],
            '[email]' => [
                'field' => 'email',
                'input' => 'textInput',
            ],
            '[phone]' => [
                'field' => 'phone',
                'input' => 'textInput',
            ],
            '[topic]' =>[
                'field' => 'topic',
                'input' => 'dropDownList',
                'items' => $this->getModel()->getContactUsTopics(),
                'inputOptions' => ['prompt' => \TS_Functions::__('Topic')]
            ],
            '[message]' => [
                'field' => 'message',
                'input' => 'textArea',
                'inputOptions' => ['placeholder' => \TS_Functions::__('What can we help you with?')]
            ],
            '[button]' => [
                'label' => \TS_Functions::__('Send'),
                'options' => ['class' => 'btn-default', 'type' => 'submit']
            ]
        ];
    }

    public function templateFields()
    {
        return [
            '[response-success]' => '_getFieldResponseSuccess',
            '[response-error]' => '_getFieldResponseError',
        ];
    }

    /**
     * @return \tradersoft\model\Contact_Us
     */
    public function getModel()
    {
        return \TSInit::$app->getVar('modelContactUs') ?: new \tradersoft\model\Contact_Us;
    }

    protected function _widget($args, $instance)
    {
        if (!($controller = $this->_initController())) {
            return false;
        }
        $controller->execute();

        $this->prepareWidget($args, $instance);
        $this->_renderTemplate();
    }

    /**
     * instance properties:
     *      'template'   => string; path to template
     *
     * Prepare widget args
     * @param $args array
     * @param $instance array
     */
    public function prepareWidget($args, $instance)
    {
        if (empty($instance['template'])) {
            $this->defaultTemplate = TS_DOCROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'home' . DIRECTORY_SEPARATOR . 'contact-us.php';
        }
        $this->templateModelName = 'modelContactUs';
        $this->_prepareArgs($args, $instance);
    }

    protected function _getName()
    {
        return \TS_Functions::__('TraderSoft Contact Us Form Widget');
    }

    protected function _getFieldResponseSuccess()
    {
        $code = '<?php';
        $code .= ' $modelContactUs = TSInit::$app->getVar(\'' . $this->templateModelName . '\');';
        $code .= ' echo $modelContactUs->successes ? $modelContactUs->responseMessage : "";';
        $code .= '?>';
        return $code;
    }

    protected function _getFieldResponseError()
    {
        $code = '<?php';
        $code .= ' $modelContactUs = TSInit::$app->getVar(\'' . $this->templateModelName . '\');';
        $code .= ' echo !$modelContactUs->successes ? $modelContactUs->responseMessage : "";';
        $code .= '?>';
        return $code;
    }
}