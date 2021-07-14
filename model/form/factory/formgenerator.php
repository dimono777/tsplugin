<?php
namespace tradersoft\model\form\factory;

use Exception;
use tradersoft\helpers\Arr;
use tradersoft\helpers\Interlayer_Crm;
use tradersoft\model\form\abstracts\AbstractForm;
use tradersoft\model\form\decorators\CRMStructure;
use tradersoft\traits\ActiveInterlayer;

class FormGenerator
{
    use ActiveInterlayer;

    protected static $_cache;

    protected $_params;

    /**
     * @var AbstractForm
     */
    protected $_model;
    protected $_savedData = [];

    /**
     * @param array $params
     *
     * @return AbstractForm
     * @throws Exception
     */
    public static function createModel(array $params)
    {
        return (new static($params))->getModel();
    }

    /**
     * FormGenerator constructor.
     * @param array $params
     * @throws Exception
     */
    public function __construct(array $params)
    {
        $this->_checkParams($params);
        $this->_params = $params;
        $this->_initModel();
    }

    /**
     * @return AbstractForm
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareValidationErrors($validationErrors)
    {
        return $validationErrors;
    }

    /**
     * @throws Exception
     */
    protected function _initModel()
    {
        $modelName = $this->_getModelClass($this->_getFormStructureData()->getType());
        $this->_model = new $modelName($this->_getFormStructureData(), $this->_params);
        $this->_model->load([$this->_model->getName() => $this->_savedData]);
    }

    /**
     * @return CRMStructure
     * @throws \Exception
     */
    protected function _getFormStructureData()
    {
        $key = 'formData' . implode(':', $this->_params);
        $formType = $this->_params['type'];
        $apiMethod = 'get-form-' . strtolower($formType);

        if (!isset(static::$_cache[$key])) {
            $result = $this->_send($apiMethod, []);
            if (!$result->isSuccess()) {
                if (Interlayer_Crm::RESPONSE_CODE_FORM_NOT_FOUND == $result->getCode()) {
                    throw new Exception("Form not found.");
                }
                throw new Exception("Unknown error. Invalid request to CRM. Error: {$result->getMessage()}");
            }
            $formData = $result->getData();
            $this->_checkFormData($formData);
            $this->_initSavedData($formData);
            static::$_cache[$key] = new CRMStructure($formData['data']);
        }
        return static::$_cache[$key];
    }

    /**
     * @return array
     */
    protected function _getShortCodeParams()
    {
        return $this->_params;
    }

    /**
     * @param array $params
     * @throws \Exception
     */
    protected function _checkParams(array $params)
    {
        if (empty($params['type'])) {
            throw new Exception('Incorrect short code params. Param `type` must be set.');
        }

        $this->_checkFormType($params['type']);
    }

    /**
     * @param $formData
     * @throws \Exception
     */
    protected function _checkFormData($formData)
    {
        $canEdit = Arr::get($formData, 'canEdit', true);
        if (!$canEdit) {
            \TSInit::$app->request->redirect('/');
        }
        if (empty($formData['data'])) {
            throw new Exception('Incorrect form data. ' . Arr::get($formData, 'description'));
        }
        if (empty($formData['data']['fields'])) {
            throw new Exception('Incorrect form data. No form fields');
        }

        $this->_checkFormType(Arr::get($formData, 'data.type'));
    }

    /**
     * @param $type
     * @throws Exception
     */
    protected function _checkFormType($type)
    {
        $model = $this->_getModelClass($type);
        if (!class_exists($model)) {
            throw new \Exception(\TS_Functions::__('Unknown form type'));
        }
    }

    /**
     * @param array $formData
     */
    protected function _initSavedData(array $formData)
    {
        $this->_savedData = empty($formData['savedData']) ? [] : $formData['savedData'];
    }

    /**
     * @param $type
     * @return string
     */
    protected function _getModelClass($type)
    {
        return 'tradersoft\model\form\types\\' . $type;
    }
}