<?php
namespace tradersoft\helpers\abstracts;

use tradersoft\helpers\Link as HelperLink;
use tradersoft\model\ModelWithFieldInterface;
use TSInit;
use tradersoft\helpers\Widget;
use tradersoft\helpers\Html;
use tradersoft\helpers\Field;

/**
 * Active form
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
abstract class BasicForm extends Widget
{
    public $action = '';
    public $method = 'post';
    public $htmlOptions = [];
    public $errorCssClass = 'has-error';
    public $successCssClass = 'has-success';
    public $ajaxEnable = false;
    public $ajaxUrl;
    public $ajaxCallback;
    public $ajaxTrigger;
    public $enableClientValidation = true;
    public $preLoaderEnable = true;
    public $preLoaderClass = 'form-pre-loader';
    protected $_attributes = [];


    /**
     * @param $config array
     */
    public function __construct(array $config = [])
    {
        $this->_configure($config);
        $this->_initOptions();
        $this->init();
    }

    /**
     * @param ModelWithFieldInterface $model
     * @param string                  $attribute
     * @param array                   $options
     *
     * @return Field
     */
    abstract public function createField(ModelWithFieldInterface $model, $attribute, $options = []);

    /**
     * Init form
     */
    public function init()
    {}

    public function formStart()
    {
        return Html::beginForm($this->action, $this->method, $this->htmlOptions);
    }

    public function formEnd()
    {
        return Html::endForm();
    }

    public function addClientOption(array $clientOption)
    {
        $this->_attributes[] = $clientOption;
    }

    /**
     * @return array
     */
    protected function _getClientOptions()
    {
        return $this->_attributes;
    }

    protected function _getFormSettings()
    {
        $settings = [
            'preLoaderEnable' => $this->preLoaderEnable,
            'preLoaderClass' => $this->preLoaderClass
        ];
        if ($this->ajaxEnable && !empty($this->ajaxUrl) && !empty($this->ajaxCallback)) {

            if (!HelperLink::hasCurrentDomain($this->ajaxUrl)) {
                $this->ajaxUrl = TSInit::$app->request->getLink($this->ajaxUrl);
            }

            $settings = array_merge($settings, [
                'ajaxEnable' => $this->ajaxEnable,
                'ajaxUrl' => $this->ajaxUrl,
                'ajaxCallback' => $this->ajaxCallback
            ]);
            if ($this->ajaxTrigger) {
                $settings['ajaxTrigger'] = $this->ajaxTrigger;
            }
        }

        return $settings;
    }

    /**
     * Add domain to action link but clear protocol
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param string $action
     */
    protected function _setAction($action)
    {
        $action = TSInit::$app->request->getLink($action);

        $actionParts = wp_parse_url($action);
        /** need to clear URL scheme */
        if (isset($actionParts['scheme']) && $actionParts['scheme']) {
            $actionParts['scheme'] = \TS_Functions::getProtocol();
            $action = \tradersoft\helpers\Link::httpBuildUrl($actionParts);
        }

        $this->action = $action;
    }

    /**
     * Add domain to ajax url
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param string $ajaxUrl
     */
    protected function _setAjaxUrl($ajaxUrl)
    {

        $ajaxUrlParts = wp_parse_url($ajaxUrl);
        /** need to clear URL scheme */
        if (isset($ajaxUrlParts['scheme']) && $ajaxUrlParts['scheme']) {
            $ajaxUrlParts['scheme'] = \TS_Functions::getProtocol();
            $ajaxUrl = \tradersoft\helpers\Link::httpBuildUrl($ajaxUrlParts);
        }
        $this->ajaxUrl = $ajaxUrl;
    }

    /**
     * @return string
     */
    public function getPreLoaderBlock()
    {
        $preLoaderBlock = '';
        if ($this->preLoaderEnable) {
            $preLoaderBlock .= Html::beginTag('div', ['class' => $this->preLoaderClass, 'style'=>'display:none;']);
            $preLoaderBlock .= Html::beginTag('span', ['class' => 'spin-loader']);
            $preLoaderBlock .= Html::endTag('span');
            $preLoaderBlock .= Html::endTag('div');
        }
        return $preLoaderBlock;
    }

    /**
     * Set properties
     * @param $properties array
     */
    private function _configure(array $properties)
    {
        foreach ($properties as $name => $value) {
            if (property_exists($this, $name)) {

                /** @var string If there is a method for setting this property, we use it */
                $method = "_set" . ucfirst($name);
                if (method_exists($this, $method)) {
                    $this->$method($value);
                } else {
                    $this->$name = $value;
                }
            }
        }
    }

    private function _initOptions(){
        if (empty($this->htmlOptions['id'])) {
            $this->htmlOptions['id'] = uniqid();
        }
    }

}