<?php

namespace tradersoft\model;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Html;
use tradersoft\helpers\Interlayer_Crm;

use TSInit;
use TS_Functions;

class LeadSuitabilityApplication
{
    const STATUS_NOT_APPLICABLE = 1;
    const STATUS_APPROVED_RETAIL = 2;
    const STATUS_PROFESSIONAL_ELIGIBLE = 3;
    const STATUS_PROFESSIONAL_REQUESTED = 4;
    const STATUS_PROFESSIONAL_APPLICATION_SUBMITTED = 5;
    const STATUS_ELECTIVE_PROFESSIONAL = 6;
    const STATUS_PROFESSIONAL_APPLICATION_REJECTED = 7;
    const STATUS_ELECTIVE_RETAIL = 8;

    /** @var string */
    protected $_leadId;

    protected $_leadApplicationData = [];

    /**
     * Survey constructor.
     */
    public function __construct()
    {
        $this->_leadId = TSInit::$app->trader->get('crmId', 0);
    }

    /**
     * Getting lead suitability application data from CRM
     *
     * @param bool $force
     *
     * @return array
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    protected function _fetchLeadApplicationData($force = false)
    {
        if (!$this->_leadId) {
            return [];
        }

        static $dataReceived = false;
        if (!$dataReceived || $force) {
            $dataReceived = true;
            $this->_leadApplicationData = $this->_getApiResponseData(
                Interlayer_crm::getLeadSuitabilityApplicationData($this->_leadId)
            );
        }

        return $this->_leadApplicationData;
    }

    /**
     * Checking can professional form show
     *
     * @return bool
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public function canShow()
    {
        return Arr::get($this->_fetchLeadApplicationData(), 'professionalSuitabilityAvailable', false)
            && TS_Functions::issetLink('[TS-PROFESSIONAL-REQUEST-FORM]');
    }

    /**
     * Checking can lead professional request send
     *
     * @return bool
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public function canSendRequest()
    {
        return (bool) Arr::get($this->_fetchLeadApplicationData(), 'canSendProfessionalRequest', false);
    }

    /**
     * Getting questions
     *
     * @return mixed
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public function getQuestions()
    {
        return Arr::get($this->_fetchLeadApplicationData(), 'questions', []);
    }

    /**
     * Getting minimum answered questions
     *
     * @return int
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public function getMinNumberAnsweredQuestions()
    {
        return Arr::get(
            $this->_fetchLeadApplicationData(),
            'minNumberAnsweredQuestions',
            count($this->getQuestions())
        );
    }

    /**
     * Getting lead application status ID
     *
     * @return mixed
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public function getCurrentLeadApplicationStatus()
    {
        return Arr::get($this->_fetchLeadApplicationData(), 'leadSuitabilityApplicationStatusId');
    }

    /**
     * Sending professional request
     *
     * @param array $answeredQuestions
     *
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public function send(array $answeredQuestions)
    {
        $questions = $this->getQuestions();

        foreach ($answeredQuestions as $answerId => $answeredQuestion) {
            $questionText = Arr::get($questions, $answerId);

            if (!empty($questionText)) {
                $answeredQuestions[$answerId] = Html::encode(TS_Functions::__($questionText));
            }
        }

        $this->_getApiResponseData(
            Interlayer_crm::sendLeadProfessionalRequest($this->_leadId, $answeredQuestions)
        );
    }


    /**
     * Parsing response data
     *
     * @param array $response
     * @param string $resultParam
     *
     * @return array|mixed
     */
    private function _getApiResponseData($response, $resultParam = 'data')
    {
        return $resultParam ? Arr::get($response, $resultParam) : $response;
    }
}