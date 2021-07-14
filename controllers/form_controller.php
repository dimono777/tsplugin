<?php

namespace tradersoft\controllers;

use tradersoft\controllers\interfaces\IControllerWithModel;
use tradersoft\model\form\FormManager;
use TSInit;
use tradersoft\View;
use tradersoft\model\Media_Queue;

/**
 * Class Form_Controller
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Form_Controller extends Base_Controller implements IControllerWithModel
{
    protected $_templatesPath = '/templates/formbuilder/';

    /**
     * @var FormManager
     */
    protected $_formManager;

    public function getModel()
    {
        return $this->_formManager;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'actionIndex' => $this->_formManager->getAccessRight()
        ];
    }

    /**
     * @throws \Exception
     */
    public function actionIndex()
    {
        $model = $this->_formManager->getModel();
        $post = TSInit::$app->request->post();

        if (TSInit::$app->request->isPost && $model->load($post) && $model->validate()) {
            $result = $model->save();

            if($model->isNeedRedirect()) {
                $this->redirect($model->getRedirectUrl());
            }

            if (\TSInit::$app->request->isAjax) {
                $this->_jsonResponse([
                    'isSuccess' => $result && !$model->hasErrors(),
                    'validationErrors' => $model->getErrors(),
                    'systemMessages' => $model->getSystemMessages()
                ]);
            }
        }
    }

    /**
     * @param $expression
     * @throws \Exception
     */
    public function render($expression = '')
    {
        $content = $this->_formManager->getFormContent() . $this->_getStaticTemplates();

        View::setContent($content, $expression);
    }

    protected function _loadMediaFiles()
    {
        $params = [
            Media_Queue::FILTER_KEY_INITIATOR => static::class,
            Media_Queue::FILTER_KEY_FORM_TYPE => $this->_formManager->getFormType(),
            Media_Queue::FILTER_KEY_FB_SHOW_DEFAULT_STYLES => !$this->_formManager->isDisableDefaultStyles(),
        ];

        Media_Queue::getInstanceByParams($params)->enqueue();
    }

    /**
     * @return false|string
     */
    protected function _getStaticTemplates()
    {
        ob_start();
        ob_implicit_flush(false);
        try {
            $this->_buildPopupForInvalidCountries();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return ob_get_clean();
    }

    protected function _buildPopupForInvalidCountries()
    {
        $path = rtrim(TS_DOCROOT, '/') . $this->_templatesPath . "popupforinvalidcountries.php";
        require_once($path);
    }

    protected function _startExecute()
    {
        parent::_startExecute();
        $this->_formManager = new FormManager($this->params);
    }
}