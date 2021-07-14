<?php
namespace tradersoft;

use tradersoft\controllers\Default_Controller;
use tradersoft\helpers\Arr;
use tradersoft\helpers\Page;
use tradersoft\helpers\System\PageKey;

/**
 * Account details model
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Router
{
    public static function init()
    {
        $post = get_post();

        // get list of short codes (for example [TS-REGISTRATION]) in current page's content
        $pageKeys = ($post)
            ? Page::getKeysById($post->ID)
            : [];

        if ($pageKeys) {
            $defaultController = self::_loadDefaultController();

            // replace each short code by rendered view using specific controller (or default, if doesn't exist)
            foreach ($pageKeys as $row) {

                $controller = self::loadControllerByShortCode(Arr::get($row, 'key'));

                $params = Arr::get($row, 'params', []);
                $viewPath = Arr::get($row, 'path');
                $renderExpr = Arr::get($row, 'expression', '');

                if (!$controller) {
                    $params = [];
                    $viewPath = null;
                    $controller = clone $defaultController;
                }

                $controller
                    ->setParams($params)
                    ->setView($viewPath)
                    ->setViewExpr($renderExpr);

                \TSInit::$app->request->addController($controller);
            }
        } else {
            $controller = self::loadControllerByUrl();
            if ($controller) {
                \TSInit::$app->request->addController($controller);
            }
        }

    }

    /**
     * Init controller by short code
     * @param string $shortCode
     * @return \tradersoft\controllers\Base_Controller|false
     */
    public static function loadControllerByShortCode($shortCode)
    {
        try {
            $keys = PageKey::getPagesActions();
            if (empty($shortCode) || empty($keys[$shortCode])) {
                return null;
            }
            preg_match('/^([\w-]+)\/?([\w-]*)/', $keys[$shortCode], $matches);
            if (empty($matches[1])) {
                return null;
            }

            $action = '';
            $routeAction = str_replace(' ', '', ucwords(str_replace('-', ' ', $matches[2])));
            if (!empty($routeAction)) {
                $action = 'action' . $routeAction;
            }

            $controllerName =  self::getFullControllerName($matches[1]);
            if(!$controllerName){
                return null;
            }
            /** @var \tradersoft\controllers\Base_Controller $controller */
            return new $controllerName($action);

        } catch (\Exception $e) {
            wp_die($e->getMessage());
        }
    }

    /**
     * Init controller by URI
     * @return \tradersoft\controllers\Base_Controller|null
     */
    public static function loadControllerByUrl()
    {
        $path = parse_url(\TSInit::$app->request->getPath());
        if (empty($path['path'])) {
            return null;
        }
        $path = trim($path['path'],'/');
        if (strpos($path, '//') !== false) {
            return null;
        }
        if (strpos($path, '/') === false) {
            return null;
        }

        /** @var string $languages */
        $languages = implode('|', array_keys(\TS_Functions::getActiveLanguages()));

        // Remove the current language from the path
        $path = preg_replace(
            "/^($languages)(\/|$)/",
            '',
            $path
        );

        list ($controllerName, $action) = explode('/', $path, 2);
        if (empty($controllerName) || empty($action)) {
            return null;
        }

        $action = self::_tryToGetAction($action);


        $controllerName =  self::getFullControllerName($controllerName);
        if(!$controllerName){
            return null;
        }
        /** @var \tradersoft\controllers\Base_Controller $controller */
        return new $controllerName($action);
    }

    /**
     * @param $controller string
     * @return string
     */
    private static function getFullControllerName($controller)
    {
        $controllerName = mb_strtolower($controller, 'UTF-8');
        $controllerName = str_replace(' ', '_', ucwords(str_replace('-', ' ', $controllerName)));
        $controllerName .= '_Controller';
        $controllerPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'controllers'
                          . DIRECTORY_SEPARATOR . mb_strtolower($controllerName, 'UTF-8'). '.php';
        if (!file_exists($controllerPath)) {
            return '';
        }
        $class = '\tradersoft\controllers\\' . $controllerName;
        /** @var $controller \tradersoft\controllers\Base_Controller*/
        if (!class_exists($class)) {
            return '';
        }

        return $class;
    }

    /**
     * Function _loadDefaultController
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return Default_Controller
     */
    private static function _loadDefaultController()
    {
        return new Default_Controller();
    }

    /**
     * Function _tryToGeAction
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param string $string
     *
     * @return string
     */
    private static function _tryToGetAction($string)
    {
        $action = '';
        $routeAction = str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
        if (!empty($routeAction)) {
            $action = 'action' . $routeAction;
        }

        return $action;
    }
}