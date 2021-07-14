<?php

namespace tradersoft\controllers;

use TSInit;
use tradersoft\helpers\Link;
use tradersoft\helpers\multi_language\Multi_Language;
use tradersoft\model\LeadSuitabilityApplication;

class Professional_Controller extends Base_Controller
{
    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            'actionIndex' => ['roles' => '@'],
            'actionSendRequest' => ['roles' => '@'],
        ];
    }

    /**
     * Rendering request form or index page by lead suitability application status
     *
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public function actionIndex()
    {
        $leadSuitabilityApplication = new LeadSuitabilityApplication();

        if (!$leadSuitabilityApplication->canShow()) {
            $this->goHome();
        }

        if ($leadSuitabilityApplication->canSendRequest()) {
            $this->view = 'professional/request_form';
            $language = Multi_Language::getInstance()->getCurrentLanguage();
            $this->_setVars([
                'questions' => $leadSuitabilityApplication->getQuestions(),
                'minNumberAnsweredQuestions' => $leadSuitabilityApplication->getMinNumberAnsweredQuestions(),
                'sendRequestUrl' => implode('/', array_filter([
                    $language && $language != 'en' ? $language : null,
                    'professional/sendRequest',
                ])),
            ]);
        } else {
            $this->view = 'professional/index';
            $this->_setVars($this->_getViewModes($leadSuitabilityApplication->getCurrentLeadApplicationStatus()));
        }

        $this->render();
    }

    /**
     * Sending professional request
     *
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public function actionSendRequest()
    {

        if (!empty($_POST['questions'])) {
            (new LeadSuitabilityApplication())->send($_POST['questions']);
        }

        $this->redirect(Link::getTraderProfessionalForm());
    }

    /**
     * @inheritDoc
     */
    protected function _beforeExecute()
    {
        if (!TSInit::$app->trader->professionalSuitabilityAvailable) {
            $this->goHome();
        }
    }

    /**
     * @param int $currentLeadStatusId
     *
     * @return array
     *
     * @author Alex Dryn <alexandr.dryn@tstechpro.com>
     */
    protected function _getViewModes($currentLeadStatusId)
    {
        return [
            'requestInProcess' => $currentLeadStatusId == LeadSuitabilityApplication::STATUS_PROFESSIONAL_REQUESTED,
            'docsReviewInProcess' => $currentLeadStatusId == LeadSuitabilityApplication::STATUS_PROFESSIONAL_APPLICATION_SUBMITTED,
            'rejected' => $currentLeadStatusId == LeadSuitabilityApplication::STATUS_PROFESSIONAL_APPLICATION_REJECTED,
            'alreadyProfessional' => $currentLeadStatusId == LeadSuitabilityApplication::STATUS_ELECTIVE_PROFESSIONAL
        ];
    }
}