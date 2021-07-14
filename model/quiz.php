<?php

namespace tradersoft\model;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Interlayer_Crm;

/**
 * Class Quiz
 * @package tradersoft\model
 */
class Quiz
{
    /** @var string */
    protected $_leadId;

    protected $_validationErrors = [];

    protected $_allowed = true;


    /**
     * Quiz constructor.
     */
    public function __construct()
    {
        $this->_leadId = \TSInit::$app->trader->get('crmId', 0);
        $this->checkIsAllowed();
    }

    /**
     * Get full data
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     *
     * @return mixed
     */
    public function getFullData()
    {

        if (!$this->_allowed) {
            return [];
        }

        $response = Interlayer_Crm::getQuizFullData(
            $this->_leadId
        );


        $code = Arr::get($response, 'returnCode', Interlayer_Crm::RESPONSE_CODE_UNSPECIFIED_ERROR);
        $data = Arr::get($response, 'data', []);

        switch ($code) {
            case Interlayer_Crm::RESPONSE_CODE_ACCOUNT_NOT_FOUND:
            case Interlayer_Crm::RESPONSE_CODE_NO_ALLOWED_QUIZZES:
                $this->_allowed = false;
                break;
            case Interlayer_Crm::RESPONSE_CODE_UNSPECIFIED_ERROR:
                $data = [];
                break;
        }

        return $data;
    }

    /**
     * Function ValidationErrors
     * Get _validationErrors value
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @return array
     */
    public function getValidationErrors()
    {

        return $this->_validationErrors;
    }


    /**
     * Function checkIsAllowed
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return void
     */
    public function checkIsAllowed()
    {
        \TSInit::$app->trader->updateTraderInfo();
        $this->_allowed = \TSInit::$app->trader->get('showQuiz', false);
    }

    /**
     * Function Allowed
     * Get _allowed value
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @return bool
     */
    public function isAllowed()
    {

        return $this->_allowed;
    }

    /**
     * Function saveCurrentLeadAnswers
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param array $submissionResult
     *
     * @return bool
     */
    public function saveCurrentLeadAnswers(array $submissionResult)
    {
        $response = Interlayer_Crm::sendQuizResults(
            $this->_leadId,
            $submissionResult
        );
        $this->_validationErrors = [];
        $code = Arr::get($response, 'returnCode', Interlayer_Crm::RESPONSE_CODE_UNSPECIFIED_ERROR);

        switch ($code) {
            case Interlayer_Crm::RESPONSE_CODE_REQUIRED_FIELD_MISSING:
            case Interlayer_Crm::RESPONSE_CODE_FIELD_NOT_VALID:
                $this->_validationErrors = Arr::get($response, 'validationErrors', []);
                break;

            case Interlayer_Crm::RESPONSE_CODE_ACCOUNT_NOT_FOUND:
            case Interlayer_Crm::RESPONSE_CODE_NO_ALLOWED_QUIZZES:
                $this->_allowed = false;
                break;

            case Interlayer_Crm::RESPONSE_CODE_MAX_QUIZ_SUBMIT_ATTEMPTS:
                $code = Interlayer_Crm::RESPONSE_CODE_SUCCESS;
                break;
        }

        return ($code == Interlayer_Crm::RESPONSE_CODE_SUCCESS);
    }
}