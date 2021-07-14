<?php
namespace tradersoft\controllers;

use TSInit;
use \TS_Functions;
use \tradersoft\helpers\Arr;
use tradersoft\helpers\Page;
use \tradersoft\model\Survey as ModelSurvey;

use tradersoft\exceptions\InvalidSurveyTypeException;

/**
 * Survey controller
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Survey_Controller extends Base_Controller
{
    /** @var ModelSurvey */
    protected $_survey;

    public function rules()
    {
        return [
            'actionIndex' => [
                'roles' => '@', //Only for authorization user
            ],
        ];
    }

    /**
     * Process submit survey action
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     */
    public function actionIndex()
    {
        try {
            // trying to getting survey
            $this->_getSurvey();

            if ($this->_survey->getSurveyTypeId() == ModelSurvey::SURVEY_TYPE_IMPROVED) {
                return $this->_renderImprovedSurvey();
            }

            if (\TSInit::$app->request->isPost) { // submitted

                $this->_survey->updateCurrentLeadResult(
                    Arr::get($_POST, 'question', [])
                );

                switch (Arr::get($_POST, 'pageAction')) {
                    case 'submit':
                        $this->_processSubmit();
                        break;

                    case 'next':
                        $this->_processNextPage();
                        break;
                    case 'prev':
                        $this->_processPrevPage();
                        break;

                    default:
                        $this->_show();
                        break;
                }

            } else {
                $this->_show();
            }

        } catch (InvalidSurveyTypeException $e) {
            Page::set404();
        } catch (\Exception $e) {
            wp_die($e->getMessage());
        }

    }

    /**
     * Render view for improved survey
     *
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    protected function _renderImprovedSurvey()
    {
        $this->view = 'survey/improved';
        $surveyData = $this->_survey->getInitialData();
        $urlWithoutProtocol = str_replace(
            ['https://', 'http://', '//'], '', $surveyData['url']
        );

        $this->_setVars([
            'completeRedirectUrl' => $this->_survey->getRedirectUrlAfter(),
            'homeUrl' => TSInit::$app->request->getHomeUrl(),
            'urlWithoutProtocol' => rtrim($urlWithoutProtocol, '/'),
            'url' => $surveyData['url'],
        ]);

        $this->render();
        return;
    }

    /**
     * Process submit survey action
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     */
    private function _processSubmit()
    {
        /** Get current lead answers */
        if (!$this->_survey->getCurrentLeadAnswers()) {
            $this->_show();
            return;
        }

        /** Check questions mandatory not filled */
        if ($mandatoryNotFilled = $this->_survey->getNotFilledMandatoryFields()) {
            $this->_show($mandatoryNotFilled);
            return;
        }

        /** Save result process */
        if ($this->_survey->saveCurrentLeadAnswers()) {
            /** Reset lead survey data, for next show survey form. We take last answers as pre result */
            $this->_survey->setCurrentLeadAnswers([]);
            $this->_survey->setCurrentPageId(ModelSurvey::PAGE_BY_DEFAULT);


            /** Redirect when submitted if the link is not empty
             * @var $redirectUrl string
             */
            if ($redirectUrl = $this->_survey->getRedirectUrlAfter()) {

                $this->_redirectAfterSubmit($redirectUrl);

            } else {

                /** Get success page */
                \TSInit::$app->session->setFlash('surveySuccess', true);
            }
        }

        $this->_show();
    }

    /**
     * Process survey previous page
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     */
    private function _processPrevPage()
    {
        $currentPageId = $this->_survey->getCurrentPageId();
        $this->_survey->setCurrentPageId(--$currentPageId);

        $this->_show();
    }

    /**
     * Process next survey page
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     */
    private function _processNextPage()
    {
        $currentPageId = $this->_survey->getCurrentPageId();

        $mandatoryNotFilled = $this->_survey->getNotFilledMandatoryFields();

        /** If has not filled mandatory questions, set old page */
        if (!$mandatoryNotFilled) {
            /** Set next page */
            $this->_survey->setCurrentPageId($currentPageId + 1);
        }

        $this->_show($mandatoryNotFilled);
    }

    /**
     * Set surveys data
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
     * @param array $mandatoryNotFilled
     */
    private function _show(array $mandatoryNotFilled = [])
    {
        $this->_setVar('surveyData', [
            'haveValidationFails' => (bool) $mandatoryNotFilled,
            'tree' => $this->_survey->getSurveyFormHtml($mandatoryNotFilled),
            'lang' => TS_Functions::getCurrentLanguage(),
            'pageId' => $this->_survey->getCurrentPageId(),
            'totalQuestionsCountByPages' => $this->_survey->getQuestionsCountByPage(),
            'totalPagesCount' => $this->_survey->getPageCount(),
        ]);
    }

    /**
     * Redirect after process submitting survey action if the link is not empty
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @param $link
     */
    protected function _redirectAfterSubmit($link)
    {

        if (!empty($link)) {
            $this->redirect($link);
        }
    }


    /**
     * Getting survey model
     *
     * @return ModelSurvey
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     * @throws InvalidSurveyTypeException
     */
    protected function _getSurvey()
    {
        if ($this->_survey) {
            return $this->_survey;
        }

        $this->_survey = new ModelSurvey();
        if (!$this->_survey || !in_array($this->_survey->getSurveyTypeId(), ModelSurvey::AVAILABLE_SURVEY_TYPES)) {
            throw new InvalidSurveyTypeException('Invalid survey');
        }

        return $this->_survey;
    }
}