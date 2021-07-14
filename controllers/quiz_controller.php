<?php

namespace tradersoft\controllers;

use \TSInit;
use \tradersoft\helpers\Arr;
use \tradersoft\helpers\Platform;
use \tradersoft\model\Quiz as ModelQuiz;

/**
 * Class Quiz_Controller
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 */
class Quiz_Controller extends Base_Controller
{
    /** @var ModelQuiz */
    protected $_model;

    public function rules()
    {
        return [
            'actionIndex' => [
                'roles' => '@', // Only for authorization user
            ],
        ];
    }

    /**
     * Quiz page
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     */
    public function actionIndex()
    {
        $this->_getTemplateVariables();
    }

    /**
     * Processes the submission of the form
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     */
    public function actionSubmit()
    {
        /** @var array $result */
        $result = ['success' => false, 'validationErrors' => []];

        if (
            !TSInit::$app->request->isAjax
            || !TSInit::$app->request->isPost
        ) {
            $this->_jsonResponse($result);
        }

        /** @var array $submissionResult */
        $submissionResult = $this->_prepareQuestionsResults(Arr::get($_POST, 'results', []));

        /** Save result process */
        $result['success'] = $this->_model->saveCurrentLeadAnswers($submissionResult);


        /** Get errors, if there is */
        $result['validationErrors'] = array_filter(
            Arr::extract($this->_model->getValidationErrors(), ['questionsIds'], [])
        );

        if ($result['success']) {

            $result['message'] = '';

        } elseif (
            !$result['success']
            && $result['validationErrors']
        ) {

            $result['message'] = \TS_Functions::__('Validation Errors');

        } else {

            $result['message'] = \TS_Functions::__('Unknown Error. Please try again');

        }
        $this->_jsonResponse($result);
    }

    protected function _beforeExecute()
    {
        $this->_model = new ModelQuiz();
        if (!$this->_model->isAllowed()) {
            $this->redirect(
                Platform::getURL()
            );
        }
    }

    /**
     * Get tempalte variables
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     */
    protected function _getTemplateVariables()
    {
        $this->_setVar(
            'templateVariables',
            [
                'fullData' => $this->_model->getFullData(),
                'ajaxUrl' => TSInit::$app->request->getLink('quiz/submit'),
                'defaultError' =>  \TS_Functions::__('Unknown Error. Please try again'),
            ]
        );
    }

    /**
     * Function _prepareQuestionsResults
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $raw
     *
     * @return array
     */
    protected function _prepareQuestionsResults($raw)
    {
        $prepared = [];
        foreach ($raw as $questionId => $answers) {
            $prepared['questions'][] = [
                'id' => $questionId,
                'answers' => $this->_prepareAnswersResults($answers),
            ];
        }
        return $prepared;
    }

    /**
     * Function _prepareAnswersResults
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $raw
     *
     * @return array
     */
    protected function _prepareAnswersResults($raw)
    {
        $prepared = [];
        foreach ($raw as $answerId => $answerValue) {
            $data = [
                'id' => $answerId,
            ];

            if (!is_array($answerValue)) {
                $data['value'] = (!is_array($answerValue)) ? $answerValue : null;
            }

            $prepared[] = $data;
        }
        return $prepared;
    }
}