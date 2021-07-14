<?php

namespace tradersoft\model;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Assets;
use tradersoft\helpers\Config;
use tradersoft\View;

/**
 * Class Media_Queue
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package tradersoft\model
 */
class Media_Queue
{
    /** @var string  */
    const FILTER_KEY_INITIATOR = 'initiator';

    /** @var string  */
    const FILTER_KEY_ACTION = 'action';

    /** @var string  */
    const FILTER_KEY_FORM_TYPE = 'type';

    /** @var string  */
    const FILTER_KEY_WP_ADMIN = 'wp-admin';

    /**
     * Parameter value must be bool. Whether to show the default styles.
     * @var string
     */
    const FILTER_KEY_FB_SHOW_DEFAULT_STYLES = 'show';

    /** @var array */
    protected static $_instances = [];

    /** @var array */
    protected $_stylesList = [];

    /** @var array */
    protected $_scriptsList = [];

    /** @var array  */
    protected $_inlineScripts = [];

    /** @var array  */
    protected static $_inlineScriptsAdded = [];

    /** @var array  */
    protected $_params = [];

    /**
     * Function getInstanceByParams
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param array $params
     *
     * @return static
     */
    public static function getInstanceByParams(array $params = [])
    {

        ksort($params);
        $paramsUniqueKey = hash('sha256', json_encode($params));
        if (!isset(static::$_instances[$paramsUniqueKey])) {
            static::$_instances[$paramsUniqueKey] = new static($params);
        }

        return static::$_instances[$paramsUniqueKey];
    }

    /**
     * Function getInstanceByInitiatorOnly
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $initiator
     *
     * @return static
     */
    public static function getInstanceByInitiatorOnly($initiator)
    {

        return static::getInstanceByParams(
            [
                static::FILTER_KEY_INITIATOR => $initiator,
            ]
        );
    }

    /**
     * Function getInstanceByInitiatorAndActionOnly
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param string $initiator
     * @param string $action
     *
     * @return static
     */
    public static function getInstanceByInitiatorAndActionOnly($initiator, $action)
    {

        return static::getInstanceByParams(
            [
                static::FILTER_KEY_INITIATOR => $initiator,
                static::FILTER_KEY_ACTION => $action,
            ]
        );
    }

    /**
     * Function __construct
     * Media_Queue constructor.
     *
     * @param array $params
     */
    public function __construct(array $params = [])
    {

        $this->_params = $params;
        $this->_params[self::FILTER_KEY_WP_ADMIN] = is_admin();
        $this->_loadScripts();
        $this->_loadScriptsInline();
        $this->_loadStyles();
    }

    /**
     * Function addScriptInline
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $body
     * @param string $handle
     *
     * @param bool $afterScripts
     *
     * @return $this
     */
    public function addScriptInline($body, $handle = '', $afterScripts = true)
    {

        if (!$body) {
            return $this;
        }
        $this->_inlineScripts[] = [
            'handle' => $handle,
            'body' => $body,
            'afterScripts' => $afterScripts,
        ];

        return $this;

    }

    /**
     * Function addScript
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param string $source
     * @param string|null $themeContainer
     * @param bool $version
     * @param string $uniqueName
     * @param array $dependsOn
     * @param bool $inFooter
     *
     * @return $this
     */
    public function addScript(
        $source,
        $themeContainer = null,
        $version = false,
        $uniqueName = '',
        array $dependsOn = [],
        $inFooter = true
    ) {

        if (!$source) {

            return $this;
        }
        $scriptItem = [
            'handle' => ($uniqueName) ? : $this->_generateAssetUniqueName($source),
            'src' => $source,
            'container' => $themeContainer,
            'deps' => $dependsOn,
            'ver' => $version,
            'inFooter' => (bool) $inFooter,
        ];
        $this->_scriptsList[] = $scriptItem;

        return $this;
    }

    /**
     * Function addStyle
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param string $source
     * @param string|null $themeContainer
     * @param bool $version
     * @param string $uniqueName
     * @param array $dependsOn
     *
     * @param bool $inFooter
     *
     * @return $this
     */
    public function addStyle(
        $source,
        $themeContainer = null,
        $version = false,
        $uniqueName = '',
        array $dependsOn = [],
        $inFooter = true
    ) {

        if (!$source) {

            return $this;
        }
        $styleItem = [
            'handle' => ($uniqueName) ? : $this->_generateAssetUniqueName($source, $themeContainer),
            'src' => $source,
            'container' => $themeContainer,
            'deps' => $dependsOn,
            'ver' => $version,
            'inFooter' => (bool) $inFooter,
        ];
        $this->_stylesList[] = $styleItem;

        return $this;
    }

    /**
     * Function enqueue
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return $this
     */
    public function enqueue()
    {

        $this->stylesEnqueue()
            ->scriptsEnqueue()
            ->scriptsInlineEnqueue();

        return $this;
    }

    /**
     * Function scriptsInlineEnqueue
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return $this
     */
    public function scriptsInlineEnqueue()
    {

        foreach ($this->_inlineScripts as $item) {
            $readyItem = [
                'handle' => null,
                'uniqueKey' => null,
                'body' => '',
                'view' => '',
                'afterScripts' => false,
            ];
            $readyItem = $item + $readyItem;
            if (!$readyItem['uniqueKey']) {

                $readyItem['uniqueKey'] = $this->_generateAssetUniqueName(
                    ($readyItem['view']) ? ($readyItem['view'] . '.php') : $readyItem['body']
                );
            }
            if (in_array($readyItem['uniqueKey'], self::$_inlineScriptsAdded)) {
                continue;
            }
            if (!$readyItem['handle']) {
                $readyItem['handle'] = $readyItem['uniqueKey'];
            }
            if (!$readyItem['body'] && $readyItem['view']) {
                $readyItem['body'] = View::load($readyItem['view'], $this->_params);
            }
            if (!$readyItem['body']) {
                continue;
            }
            add_action(
                'print_footer_scripts',
                static function() use ($readyItem) {

                    echo '<script>' . PHP_EOL . $readyItem['body'] . '</script>' . PHP_EOL;

                },
                ($readyItem['afterScripts']) ? 10 : 3
            );
            self::$_inlineScriptsAdded[] = $readyItem['uniqueKey'];

        }
        $this->_inlineScripts = [];

        return $this;
    }

    /**
     * Function stylesEnqueue
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return $this
     */
    public function stylesEnqueue()
    {
        $hasSomeInFooter = false;

        foreach ($this->_stylesList as $item) {
            $readyItem = [
                'handle' => null,
                'src' => '',
                'deps' => [],
                'ver' => false,
                'container' => null,
                'inFooter' => true,
            ];

            $readyItem = $item + $readyItem;
            if (!$readyItem['src']) {
                continue;
            }

            $readyItem['src'] = Assets::clarifyAddress(
                $readyItem['src'],
                $readyItem['container']
            );

            if (!$readyItem['handle']) {
                $readyItem['handle'] = $this->_generateAssetUniqueName($readyItem['src']);
            }

            if ($readyItem['inFooter']) {
                $hasSomeInFooter = true;
                add_action(
                    'print_footer_scripts',
                    static function() use ($readyItem) {
                        wp_enqueue_style(
                            $readyItem['handle'],
                            $readyItem['src'],
                            $readyItem['deps'],
                            $readyItem['ver']
                        );
                    },
                    1
                );
            } else {
                wp_enqueue_style(
                    $readyItem['handle'],
                    $readyItem['src'],
                    $readyItem['deps'],
                    $readyItem['ver']
                );
            }

        }
        $this->_stylesList = [];
        if ($hasSomeInFooter) {
            add_action(
                'print_footer_scripts',
                static function(){
                    wp_print_styles();
                },
                2
            );
        }
        return $this;
    }

    /**
     * Function scriptsEnqueue
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return $this
     */
    public function scriptsEnqueue()
    {

        foreach ($this->_scriptsList as $item) {
            $readyItem = [
                'handle' => null,
                'src' => '',
                'container' => null,
                'deps' => [],
                'ver' => false,
                'inFooter' => true,
            ];
            $readyItem = $item + $readyItem;
            if (!$readyItem['src']) {
                continue;
            }
            $readyItem['src'] = Assets::clarifyAddress($readyItem['src'], $readyItem['container']);
            if (!$readyItem['handle']) {
                $readyItem['handle'] = $this->_generateAssetUniqueName($readyItem['src']);
            }
            wp_enqueue_script(
                $readyItem['handle'],
                $readyItem['src'],
                $readyItem['deps'],
                $readyItem['ver'],
                $readyItem['inFooter']
            );
        }
        $this->_scriptsList = [];

        return $this;
    }

    /**
     * Function _generateAssetUniqueName
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param string $source
     * @param string|null $modifySourceForThemeContainer
     *
     * @return string
     */
    protected function _generateAssetUniqueName($source, $modifySourceForThemeContainer = null)
    {

        if (is_string($modifySourceForThemeContainer)) {
            $source = Assets::clarifyAddress($source, $modifySourceForThemeContainer);
        }

        return 'ts-' . md5($source);
    }

    /**
     * Function _loadScripts
     *  see config wp-content/plugins/tradersoft/configs/scripts.php
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return $this
     */
    protected function _loadScripts()
    {

        $this->_scriptsList = array_merge(
            $this->_scriptsList,
            $this->_filterFilesByParams(Config::get('scripts', []))
        );

        return $this;
    }

    /**
     * Function _loadScriptsInline
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return $this
     */
    protected function _loadScriptsInline()
    {

        $this->_inlineScripts = array_merge(
            $this->_inlineScripts,
            $this->_filterFilesByParams(Config::get('scriptsinline', []))
        );

        return $this;
    }

    /**
     * Function _loadStyles
     *  see config wp-content/plugins/tradersoft/configs/styles.php
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return $this
     */
    protected function _loadStyles()
    {

        $this->_stylesList = array_merge(
            $this->_stylesList,
            $this->_filterFilesByParams(Config::get('styles', []))
        );

        return $this;
    }

    /**
     * Function _filterFilesByParams
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param array $files
     *
     * @return array
     */
    protected function _filterFilesByParams(array $files)
    {

        $result = [];
        foreach ($files as $file) {
            $allowedRules = $this->_initDefaultAllowedRuleForAdmin(Arr::get($file, 'allowedForParams', []));
            if (
                $this->_isAllowedByRules($allowedRules)
                && !$this->_isForbiddenByRules(Arr::get($file, 'forbidForParams', []))
            ) {

                $result[] = $file;
            }
        }

        return $result;
    }

    /**
     * Function _isAllowedByRules
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param array $rulesCollection
     *
     * @return bool
     */
    protected function _isAllowedByRules(array $rulesCollection)
    {

        /** No rules - no restrictions */
        if (!$rulesCollection) {
            return true;
        }
        foreach ($rulesCollection as $rule) {
            /**
             * if rule is an empty but  params are not - it is not passed rule
             * so skip it and try to check next one
             */
            if (!$rule && $this->_params) {
                continue;
            }
            foreach ($rule as $param => $value) {


                /**
                 * if some param doesn't exists or has mismatch value
                 * so skip this rule at all and try to check next rule
                 */
                if (
                    !array_key_exists($param, $this->_params)
                    || $this->_params[$param] != $value
                ) {
                    continue 2;
                }

            }

            /**
             * if we are here - so all params in rule matched and whole rule is passed.
             */
            return true;

        }

        /**
         * we can be here only if all rules checked but all of them weren't passed.
         */
        return false;
    }

    /**
     * Function _isForbiddenByRules
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param array $rulesCollection
     *
     * @return bool
     */
    protected function _isForbiddenByRules(array $rulesCollection)
    {

        /** No rules - no restrictions */
        if (!$rulesCollection) {
            return false;
        }
        foreach ($rulesCollection as $rule) {

            /**
             * if rule is an empty but  params are not - it is not passed rule
             * so skip it and try to check next one
             */
            if (!$rule && $this->_params) {
                continue;
            }
            foreach ($rule as $param => $value) {


                /**
                 * if some param doesn't exists or has mismatch value
                 * so skip this rule at all and try to check next rule
                 */
                if (
                    !array_key_exists($param, $this->_params)
                    || $this->_params[$param] != $value
                ) {
                    continue 2;
                }

            }

            /**
             * if we are here - so all params in rule matched and whole rule is passed.
             */
            return false;

        }

        /**
         * we can be here only if all rules checked but all of them weren't passed.
         */
        return true;
    }

    /**
     * Checks if isset allowed rules for wp-admin params. By default scripts shouldn't be loaded with admin panel.
     *
     * @author mykyta.popov <mykyta.popov@tstechpro.com>
     *
     * @param array $rules
     *
     * @return mixed
     */
    private function _initDefaultAllowedRuleForAdmin(array $rules)
    {

        // do not care about rules outside of admin panel.
        if (!$this->_params[self::FILTER_KEY_WP_ADMIN]) {
            return $rules;
        }
        // If rules empty just add default rule that prohibits to load scripts.
        if (!$rules) {
            return [[self::FILTER_KEY_WP_ADMIN => false]];
        }
        // Check all rules if there is exists rule for admin panel. Loading scripts in admin panel should be prohibited.
        foreach ($rules as $key => $rule) {
            if (!isset($rule[self::FILTER_KEY_WP_ADMIN])) {
                $rules[$key][self::FILTER_KEY_WP_ADMIN] = false;
            }
        }

        return $rules;
    }
}