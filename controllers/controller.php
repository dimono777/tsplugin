<?php
namespace tradersoft\controllers;

use tradersoft\helpers\Arr;
use tradersoft\model\Media_Queue;
use tradersoft\View;

/**
 * Abstract base class controller
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
abstract class Controller
{
    public $view;
    public $pageTemplate;
    public $wpPost;
    public $params = [];

    protected $_accessRedirect = true;

    protected $_allowGeneralRedirect = true;

    protected $viewExpr;

    /** @var Media_Queue|null  */
    protected $_mediaFiles;

    private $action;
    private $defaultAction = 'actionDefault';

    /**
     * Function __construct
     * Controller constructor.
     *
     * @param $action
     */
    public function __construct($action = '')
    {
        $this->_setAction($action);
    }

    public function execute()
    {
        $this->_startExecute();
        $this->_beforeVerificationRights();
        $this->_access();
        $this->_setTemplate();
        $this->_setPost();
        $this->_beforeExecute();
        $this->{$this->action}();
        $this->_afterExecute();
        $this->_loadMediaFiles();

        return $this;
    }
    /**
     * Access rules
     * @return array
     *
     * For example to use:
     * ```php
     * public function rules()
     * {
     *      return [
     *          'methodName' => [
         *          'roles' => '@',
         *          'redirectUrl' => '/',
         *          'matchCallback' => function() {
         *              return true;
         *          }
     *          ]
     *      ];
     * }
     * ```
     * params:
     *      roles: string, (@, ?)
     *      redirect: bool, optional default true
     *      redirectUrl: string, optional
     *      doAction: string name controller method
     *      matchCallback: callback function, optional
     */
    public function rules()
    {
        return [];
    }

    /**
     * Redirect to URL
     * @param $url string
     */
    public function redirect($url)
    {
        \TSInit::$app->request->redirect($url);
    }

    /**
     * Redirect to /
     */
    public function goHome()
    {
        \TSInit::$app->request->redirect('/');
    }

    /**
     * DO NOT TOUCH!!!
     * For WP hook template_include
     * @param $template string
     */
    public function templateInclude($template)
    {
        return $this->pageTemplate;
    }

    /**
     * Default action
     */
    public function actionDefault()
    {}

    /**
     * @param $expression
     */
    public function render($expression = '')
    {
        View::render($this->view, $expression);
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Function AllowGeneralRedirects
     * Get _allowGeneralRedirects value
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @return bool
     */
    public function isAllowGeneralRedirect()
    {

        return $this->_allowGeneralRedirect;
    }

    /**
     * Function setView
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $view
     *
     * @return $this
     */
    public function setView($view)
    {

        $this->view = $view;
        return $this;
    }

    /**
     * Function setViewExpr
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $viewExpr
     *
     * @return $this
     */
    public function setViewExpr($viewExpr)
    {

        $this->viewExpr = $viewExpr;
        return $this;
    }

    /**
     * Function setParams
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param array $params
     *
     * @return $this
     */
    public function setParams($params = [])
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Function ViewExpr
     * Get viewExpr value
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @return mixed
     */
    public function getViewExpr()
    {

        return $this->viewExpr;
    }

    protected function _beforeVerificationRights()
    {}

    /**
     * Set path to file page template
     * @param $template string
     */
    protected function _setTemplate($template = '')
    {
        if (!empty($template) && file_exists($template)) {
            $this->pageTemplate = $template;
            add_filter('template_include', [$this, 'templateInclude'],100);
        } else {
            $this->pageTemplate = get_page_template();
        }
    }

    /**
     * Add to param WP object Post
     * Set current post
     */
    protected function _setPost()
    {
        $this->wpPost = get_post();
    }

    protected function _startExecute()
    {}

    protected function _beforeExecute()
    {}

    protected function _afterExecute()
    {}

    /**
     * Function _loadMediaFiles
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return void
     */
    protected function _loadMediaFiles()
    {
        Media_Queue::getInstanceByInitiatorAndActionOnly(static::class, $this->action)->enqueue();
    }

    /**
     * Set vars
     * @param $vars array
     */
    protected function _setVars(array $vars)
    {
        \TSInit::$app->setVars($vars);
    }

    /**
     * Set var
     * @param $name string
     * @param $value mixed
     */
    protected function _setVar($name, $value)
    {
        \TSInit::$app->setVar($name, $value);
    }

    /**
     * @param $response mixed
     */
    protected function _jsonResponse($response)
    {
        http_response_code(200);
        die(json_encode($response));
    }

    /**
     * @param $action string
     */
    private function _setAction($action = '')
    {
        if (!empty($action) && method_exists($this, $action)) {
            $this->action = $action;
        } else {
            $this->action = $this->defaultAction;
        }
    }

    /**
     * Verification of rights
     */
    private function _access()
    {
        if (!$this->_allow()) {
            $rules = $this->rules();
            if (empty($rules) || empty($rules[$this->action])) {
                return;
            }
            $rule = $rules[$this->action];
            if (isset($rule['doAction'])) {
                $this->{$rule['doAction']}();
            }
            if (Arr::get($rule, 'redirect', $this->_accessRedirect)) {
                $redirectUrl = isset($rule['redirectUrl']) ? $rule['redirectUrl'] : '/';
                $this->redirect(\TSInit::$app->request->getLink($redirectUrl));
            }
        }
    }

    private function _allow()
    {
        $rules = $this->rules();
        if (empty($rules) || empty($rules[$this->action])) {
            return true;
        }
        $rule = $rules[$this->action];
        if (!empty($rule['matchCallback'])) {
            return call_user_func($rule['matchCallback']);
        }
        if (isset($rule['roles'])) {
            if ($rule['roles'] == '@') {
                return !\TSInit::$app->trader->isGuest;
            }
            if ($rule['roles'] == '?') {
                return \TSInit::$app->trader->isGuest;
            }
        }

        return true;
    }
}