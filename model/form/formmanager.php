<?php

namespace tradersoft\model\form;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Config;
use tradersoft\model\form\factory\FormGenerator;

class FormManager
{
    private $_controllerParameters;

    /**
     * @var FormBuilderModelInterface
     */
    private $_model;

    /**
     * FormManager constructor.
     *
     * @param array $controllerParameters
     */
    public function __construct(array $controllerParameters)
    {
        $this->_controllerParameters = $controllerParameters;
        $this->_initModel();
    }

    /**
     * @return mixed
     */
    public function getAccessRight()
    {
        $formType = Arr::get($this->_controllerParameters, 'type');

        return Config::get("widget_access_rules.$formType", []);
    }

    /**
     * @return string|null
     */
    public function getFormType()
    {
        if (!$this->_model) {
            return null;
        }

        return $this->_model->getFormType();
    }

    /**
     * @return bool
     */
    public function isDisableDefaultStyles()
    {
        if (!$this->_model) {
            return false;
        }

        return $this->_model->getStructureData()->isDisableDefaultStyles();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getFormContent()
    {
        if (!$this->_model) {
            return 'Form not found';
        }

        return (new Builder($this->_model))->build();
    }

    /**
     * @return FormBuilderModelInterface
     */
    public function getModel()
    {
        return $this->_model;
    }

    private function _initModel()
    {
        try {
            $this->_model = FormGenerator::createModel($this->_controllerParameters);
        } catch (\Exception $e) {
            //TODO: Add Error log
        }
    }

}