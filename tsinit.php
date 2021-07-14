<?php

use tradersoft\components\redirect_ip_country\MainRedirect;
use tradersoft\View;
use tradersoft\helpers\Link;
use tradersoft\helpers\Page;
use tradersoft\components\DataPolicyRegistration;
use tradersoft\components\GoogleAnalytics;

/**
 * Base trader soft class
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class TSInit
{
    /** @var $app \tradersoft\Application */
    public static $app;

    private static $_instance = null;

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    private  function __construct()
    {
        self::$app = \tradersoft\Application::getInstance();
        self::$app->initTrader();

        $this->_activatePlugin();
        $this->_initWidgets();
        $this->_initPlugin();
        $this->_initContent();
        $this->_initAdminMenu();
        $this->_initWPloaded();
        $this->_initRouter();
        $this->_initDataPolicy();
        $this->_initCheckRedirect();
        $this->_initExecuteControllers();
        $this->_initPixels();
        $this->_initShortCode();
        $this->_initAdmin();
        $this->_afterSetupWP();

        \tradersoft\model\Media_Queue::getInstanceByInitiatorOnly(get_class($this))->enqueue();
    }

    private function __clone()
    {}

    private function __wakeup()
    {}

    /**
     * Register widget
     */
    private function _initWidgets()
    {
        //TODO: Remove this array after make ts_hello widget
        $oldWidget = [
            'authorization',
            'balance',
            'hello',
        ];
        $this->_initOldWidgets();
        $widgets = [];
        $dir = new \DirectoryIterator(TS_DOCROOT . 'widgets');
        foreach ($dir as $file) {
            if ($file->isFile() && $file->getExtension() == 'php') {
                //TODO: Remove this if after make ts_hello widget
                if (!in_array($file->getBasename('.php'), $oldWidget)) {
                    $className = 'tradersoft\widgets\\' .str_replace(' ', '_', ucwords(str_replace('_', ' ', $file->getBasename('.php'))));
                    if (class_exists($className)) {
                        $widgets[] = $className;
                        continue;
                    }
                }
            }
        }

        add_action("widgets_init", function () use($widgets) {
            foreach ($widgets as $widget) {
                if (class_exists($widget)) {
                    register_widget($widget);
                }
            }
        });
    }


    //TODO: Remove this method after make ts_hello widget
    private function _initOldWidgets(){
        $widgets = [
            'widgets/hello.php' => 'Hello_Widget',
        ];
        add_action("widgets_init", function () use($widgets) {
            foreach ($widgets as $path => $widget) {
                require_once(TS_DOCROOT . $path);
                if (class_exists($widget)) {
                    register_widget($widget);
                }
            }
        });
    }

    /**
     * Init admin menu
     */
    private function _initAdminMenu()
    {
        // Create settings page in admin panel
        add_action('admin_menu',  function() {
            if ( function_exists('add_menu_page') ) {
                add_menu_page(
                    'TraderSoft Options',
                    'TraderSoft',
                    8,
                    basename(__FILE__),
                    function() {
                        require_once dirname(__FILE__) . '/settings/index.php';
                });

                add_submenu_page(
                    basename(__FILE__),
                    'Smart App Banner Options',
                    'Smart App Banner',
                    8,
                    'smart-app-banner',
                    function() {
                        require_once dirname(__FILE__) . '/settings/sab-settings.php';
                });
            }
        });
    }

    /**
     * WP huk init
     */
    private function _initPlugin()
    {
        // Check for submit action
        add_action('init', function() {

            GoogleAnalytics::setClientId();

            require_once dirname(__FILE__) . '/model/submit.php';

            require_once dirname(__FILE__) . '/model/token_processing.php';

            // Auth by cookies
            if (!TSInit::$app->trader->isGuest) {
                add_filter('wp_list_pages_excludes', function() {
                    return array_merge(
                        Page::getIdsByKey('[TS-DONT-SHOW]'),
                        Page::getIdsByKey('[TS-GUEST-ONLY]')
                    );
                });
            } else {
                add_filter('wp_list_pages_excludes', function() {
                    return array_merge(
                        Page::getIdsByKey('[TS-DONT-SHOW]'),
                        Page::getIdsByKey('[TS-AUTH-ONLY]')
                    );
                });
            }

            \tradersoft\model\Smart_App_Banner::init();
        });
    }

    /**
     * WP hook the_content
     */
    private function _initContent()
    {
        add_filter('the_content', [View::class, 'theContent'], 100);
    }

    /**
     * WP hook wp_loaded
     */
    private function _initWPloaded()
    {
        //Languages
        add_action('wp_loaded', function() {
            // Change current language regarding cookie value
            \TS_Functions::switchLanguage();
        });
    }

    private function _initRouter()
    {
        add_action( 'template_redirect', ['\tradersoft\Router','init'] );
    }

    private function _initDataPolicy()
    {
        DataPolicyRegistration::applyIgnoreRedirectByIp();
    }

    private function _initCheckRedirect()
    {
        add_action(
            'template_redirect',
            function() {
                $redirectMe = true;
                /** @var \tradersoft\controllers\Base_Controller $controller */
                foreach (\TSInit::$app->request->getControllers() as $controller) {
                    $redirectMe &= $controller->isAllowGeneralRedirect();
                }

                if ($redirectMe) {
                    $ipRedirect = new MainRedirect();

                    if (!$ipRedirect->checkRules()) {
                        TSInit::$app->request->redirect($ipRedirect->getDestinationUrl());
                    }
                }
            });
    }

    private function _initExecuteControllers()
    {
        add_action(
            'template_redirect',
            function() {

            /** @var \tradersoft\controllers\Base_Controller $controller */
            foreach (\TSInit::$app->request->getControllers() as $controller) {

                try {

                    $controller->execute();
                    if ($expr = $controller->getViewExpr()) {
                        $controller->render($expr);
                    }

                } catch (\Exception $e) {
                    wp_die($e->getMessage());
                }

            }
        });
    }

    private function _initPixels()
    {
        TS_Functions::initPixels();
    }

    private function _initShortCode()
    {
        new \tradersoft\inc\Short_Code();
    }

    private function _initAdmin()
    {
        add_action('admin_footer', function() {
            $domain = \TS_Functions::getMainDomain();
            echo "<script>document.domain = '{$domain}';</script>";
        });

        add_action('save_post', function ($postId) {
            Page::clearCache();
        });
    }

    /**
     * Sets up hook for saving language information to the cookies.
     *
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     */
    protected function _afterSetupWP()
    {
        add_action('wp', function() {
            if (!is_404() && !\TSInit::$app->request->isAjax && !defined('DOING_AJAX')) {
                // Save current language in cookies
                \TS_Functions::saveLanguage();
            }
        });
    }

    /**
     * Registers activation plugin hook
     */
    protected function _activatePlugin()
    {
        $pluginFile = TS_DOCROOT . pathinfo(TS_PLUGIN_BASENAME, PATHINFO_BASENAME);

        register_activation_hook($pluginFile, function () {
                global $wpdb;

                $tableName = $wpdb->prefix . 'ts_settings';

                $createSQL = <<<SQL
CREATE TABLE `$tableName` 
(
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
    `value` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
    PRIMARY KEY (`id`)
) 
ENGINE=InnoDB 
DEFAULT CHARSET=utf8 
COLLATE=utf8_general_ci;
SQL;

                if (!function_exists('maybe_create_table')) {
                    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                }

                if(!maybe_create_table($tableName, $createSQL)){
                    wp_die('Plugin activation error: Failed to create settings table.', '', ['back_link' => true]);
                }
            }
        );
    }
}