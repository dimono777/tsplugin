<?php

namespace tradersoft\model;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Interlayer_Crm;
use tradersoft\helpers\Session;
use tradersoft\model\redirect_after_action\Init as RedirectAfterActionInit;
use tradersoft\model\redirect_after_action\actions\Survey as RedirectAfterSurvey;

use tradersoft\exceptions\InvalidSurveyTypeException;

class Survey
{
    const SURVEY_TYPE_DEFAULT = 1;
    const SURVEY_TYPE_IMPROVED = 3;

    // alternative survey type not supported
    const AVAILABLE_SURVEY_TYPES = [
        self::SURVEY_TYPE_DEFAULT,
        self::SURVEY_TYPE_IMPROVED,
    ];

    const PAGE_BY_DEFAULT = 1;

    const PAGE_ID_KEY = 'pageId';
    const RESULT_KEY = 'results';

    protected $_surveyTypeId;
    protected $_initialData = [];

    /** @var Session */
    private $_session;

    /** @var string */
    private $_leadId;

    /** @var int pages count in survey */
    private $_pagesCount;

    /**
     * Survey constructor.
     */
    public function __construct()
    {
        $this->_session = \TSInit::$app->session;

        $this->_leadId = \TSInit::$app->trader->get('crmId', 0);

        $this->_setInitialData();
    }

    /**
     * Getting survey initial data
     *
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    protected function _setInitialData()
    {
        $data = $this->_getApiResponseData(
            Interlayer_crm::getSurveyInitialData($this->_leadId)
        );

        if (empty($data['type'])) {
            return;
        }

        $this->_surveyTypeId = $data['type'];
        unset($data['type']);

        switch ($this->_surveyTypeId) {
            case static::SURVEY_TYPE_DEFAULT:
                $this->_initialData['pagesCount'] = Arr::get($data, 'pagesCount');
                $this->_pagesCount = (int) $this->_initialData['pagesCount'];

                // Check page number value in session, set default if not exist
                if (!$this->getCurrentPageId()) {
                    $this->setCurrentPageId(self::PAGE_BY_DEFAULT);
                }
                break;

            case static::SURVEY_TYPE_IMPROVED:
                $this->_initialData = $data;
                break;
        }
    }

    /**
     * Getting survey type ID
     *
     * @return int|null
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public function getSurveyTypeId()
    {
        return $this->_surveyTypeId;
    }

    /**
     * Getting initial data
     *
     * @return array
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    public function getInitialData()
    {
        return $this->_initialData;
    }

    /**
     * Get survey pages count
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return mixed
     */
    public function getSurveyPagesCount()
    {
        return $this->_getApiResponseData(
            Interlayer_crm::getSurveyPagesCount($this->_leadId)
        );
    }

    /**
     * Get current page id
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return mixed
     */
    public function getCurrentPageId()
    {
        $pageId = $this->_getLeadSurveyDataByKey(self::PAGE_ID_KEY);
        return ($pageId >= static::PAGE_BY_DEFAULT) ? $pageId : static::PAGE_BY_DEFAULT;
    }

    /**
     * Set current survey page id
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param int $pageId
     * @return bool
     */
    public function setCurrentPageId($pageId)
    {
        if ($pageId < static::PAGE_BY_DEFAULT) {
            $pageId = static::PAGE_BY_DEFAULT;
        }

        $oldPageId = $this->_getLeadSurveyDataByKey(self::PAGE_ID_KEY);

        if ($pageId == $oldPageId || $pageId > $this->_pagesCount) {
            return false;
        }
        /** Set a new page ID as current in session*/
        $this->_setLeadSurveyDataByKey(self::PAGE_ID_KEY, $pageId);

        return true;
    }

    /**
     * Get pages count
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return int
     */
    public function getPageCount()
    {
        return $this->_pagesCount;
    }

    /**
     * Get current lead answers
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return array
     */
    public function getCurrentLeadAnswers()
    {
        return $this->_getLeadSurveyDataByKey(self::RESULT_KEY, []);
    }

    /**
     * Get not filled mandatory survey fields
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return array|bool
     */
    public function getNotFilledMandatoryFields()
    {
        $result = $this->_getApiResponseData(
            Interlayer_Crm::checkSurveyMandatoryFields(
                $this->getCurrentLeadAnswers(),
                $this->getCurrentPageId(),
                $this->_leadId
            )
        );
        return ($result && is_array($result)) ? $result : [];
    }

    /**
     * Get survey info
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return mixed
     */
    public function getInfo()
    {
        return $this->_getApiResponseData(
            Interlayer_Crm::getSurveyInfo(
                $this->getCurrentPageId(),
                $this->_leadId
            )
        );
    }

    /**
     * Get survey questions count by page
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return array
     */
    public function getQuestionsCountByPage()
    {
        return $this->_getApiResponseData(
            Interlayer_crm::getQuestionsCountByPage($this->_leadId)
        );
    }

    /**
     * Set current lead answers
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param array
     */
    public function setCurrentLeadAnswers(array $answers)
    {
        $this->_setLeadSurveyDataByKey(self::RESULT_KEY, $answers);
    }

    /**
     * Save current lead answers
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @return array
     */
    public function saveCurrentLeadAnswers()
    {
        return $this->_getApiResponseData(
            Interlayer_Crm::setSurveyForm(
                $this->_leadId,
                $this->getCurrentLeadAnswers()
            )
        );
    }

    /**
     * Update surveys lead result in session
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param array $newQuestionsAndAnswers
     * @return array
     */
    public function updateCurrentLeadResult(array $newQuestionsAndAnswers)
    {
        /** @type array Get results from session */
        $resultsFromSession = $this->getCurrentLeadAnswers();

        /** @type array Get submitted valid results to array with all results of survey */
        $currentQuestionsAndAnswers = $this->_mergeNewAnswersWithOld($newQuestionsAndAnswers, $resultsFromSession);

        /**
         * If an array with all results of survey not empty
         * and it's different with session array of result
         * - set it into session array of result
         */
        if ($currentQuestionsAndAnswers && $currentQuestionsAndAnswers != $resultsFromSession) {
            $this->setCurrentLeadAnswers($currentQuestionsAndAnswers);
        }

        return $currentQuestionsAndAnswers;
    }

    /**
     * Get survey form html
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param array $mandatoryNotFilled
     * @return array
     */
    public function getSurveyFormHtml(array $mandatoryNotFilled)
    {
        return $this->_getApiResponseData(
            Interlayer_Crm::getSurveyForm(
                $this->_leadId,
                $this->getCurrentLeadAnswers(),
                $mandatoryNotFilled,
                $this->getCurrentPageId()
            )
        );
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return string
     */
    public function getRedirectUrlAfter()
    {
        return (new RedirectAfterActionInit(RedirectAfterSurvey::ID))->getUrl();
    }



    /**
     * Get lead survey data from session
     * by key with default value
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param mixed|null $key
     * @param mixed|null $default
     * @return mixed
     */
    private function _getLeadSurveyDataByKey($key = null, $default = null)
    {
        $result = $this->_session->get('surveysData', []);

        if ($key) {
            $result = Arr::path($result, $this->_leadId . '.' . $key, $default);
        }

        return $result;
    }

    /**
     * Set lead survey field data in session
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param mixed $key
     * @param mixed $value
     */
    private function _setLeadSurveyDataByKey($key, $value)
    {
        $data = $this->_getLeadSurveyDataByKey();
        $data[$this->_leadId][$key] = $value;

        $this->_session->set('surveysData', $data);
    }

    /**
     * Update new answers for lead survey
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param array $newQuestionsAndAnswers
     * @param array $currentQuestionsAndAnswers
     * @return array
     */
    private function _mergeNewAnswersWithOld(array $newQuestionsAndAnswers, array $currentQuestionsAndAnswers)
    {
        $newQuestionsAndAnswers = $this->_getApiResponseData(
            Interlayer_Crm::getValidSurveyTreeWithAnswers(
                $newQuestionsAndAnswers,
                $this->_leadId,
                $this->getCurrentPageId()
            )
        );
        if (!$newQuestionsAndAnswers || !is_array($newQuestionsAndAnswers)) {
            $newQuestionsAndAnswers = [];
        }

        // Update lead answers
        foreach ($newQuestionsAndAnswers as $newQuestionId => $newAnswer) {
            if (Arr::get($currentQuestionsAndAnswers, $newQuestionId) != $newAnswer) {
                $currentQuestionsAndAnswers[$newQuestionId] = $newAnswer;
            }
        }

        /** Get available questions and sub-questions for current page */
        $availableQuestionsForPage = $this->_getApiResponseData(
            Interlayer_Crm::getAvailableSurveysQuestionsForPage(
                $this->_leadId,
                $this->getCurrentPageId()
            )
        );

        // Clear not actual questions
        foreach ($currentQuestionsAndAnswers as $currentQuestionId => $currentAnswerId) {

            // Remove if question in from last filled page but not valid
            if (
                !isset($newQuestionsAndAnswers[$currentQuestionId])
                && in_array($currentQuestionId, $availableQuestionsForPage)
            ) {
                unset($currentQuestionsAndAnswers[$currentQuestionId]);
            }
        }

        return $currentQuestionsAndAnswers;
    }

    /**
     * @param array $response
     * @param string $resultParam
     *
     * @return array|mixed
     * @throws InvalidSurveyTypeException
     */
    private function _getApiResponseData($response, $resultParam = 'data')
    {
        if (Arr::get($response, 'returnCode') == Interlayer_Crm::RESPONSE_CODE_INVALID_SURVEY_TYPE) {
            throw new InvalidSurveyTypeException('Invalid survey type');
        }

        return $resultParam ? Arr::get($response, $resultParam) : $response;
    }
}