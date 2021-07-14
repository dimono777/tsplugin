<?php
namespace tradersoft\widgets\base;

use tradersoft\model\Media_Queue;

/**
 * Base widget.
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
abstract class Widget extends \WP_Widget
{

    /** @var Media_Queue  */
    protected $_mediaFiles;
    /**
     * @param string $id_base
     * @param string $name
     * @param array  $widget_options
     * @param array  $control_options
     */
    public function __construct()
    {
        $this->_init();
        parent::__construct(
            $this->_getId(),
            $this->_getName(),
            $this->_getWidgetOptions(),
            $this->_getControlOptions()
        );

        $this->_mediaFiles = Media_Queue::getInstanceByInitiatorOnly(get_class($this));
        $this->_loadInlineScripts();
    }

    public function widget($args, $instance)
    {
        $this->_widget($args, $instance);
        $this->_mediaFiles->enqueue();
    }

    public function update( $new_instance, $old_instance )
    {
        $instance = $new_instance;
        return $instance;
    }

    public function form( $instance )
    {
    }

    public function rules()
    {
    }

    protected function _widget($args, $instance)
    {

        $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
        $title = !empty( $instance['title'] ) ? $instance['title'] : '';
        $text = !empty( $instance['text'] ) ? $instance['text'] : '';

        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        if (!empty($text)) {
            echo '<div class="textwidget">' . $text . '</div>';
        }

    }

    /**
     * Return widget name
     * @return string
     */
    abstract protected function _getName();

    /**
     * Return ID base for widget
     * @return string
     */
    protected function _getId()
    {
        return str_replace('\\', '.', mb_strtolower(get_class($this), 'UTF-8'));
    }

    /**
     * Return class name for widget
     * @return string
     */
    protected function _className()
    {
        return mb_strtolower(get_class($this), 'UTF-8');
    }

    /**
     * Return widget options
     * @return array
     */
    protected function _getWidgetOptions()
    {
        return [
            'classname' => $this->_className(),
            'description' => $this->_getDescription(),
            'customize_selective_refresh' => true
        ];
    }

    /**
     * Return control options for widget
     * @return array
     */
    protected function _getControlOptions()
    {
        return [];
    }

    /**
     * Return description for widget
     * @return string
     */
    protected function _getDescription()
    {
        return $this->_getName();
    }

    /**
     * Render template file
     * @param string $renderFile
     * @param array  $params
     * @param bool $ignoreMediaFiles
     * @return string
     */
    protected function _render($renderFile, $params = [])
    {
        $viewFile = $this->_findViewFile($renderFile);

        ob_start();
        ob_implicit_flush(false);
        extract($params, EXTR_OVERWRITE);
        require($viewFile);

        return ob_get_clean();
    }

    /**
     * Find template file
     * @param string $renderFile
     * @param array  $params
     * @return string
     */
    protected function _findViewFile($renderFile)
    {
        $templates = [
            $renderFile,
            get_template_directory() . '/widgets/views/' . $renderFile . '.php',
            get_template_directory() . '/widgets/views/' . $this->_getClassName() . '/' . $renderFile . '.php',
            TS_DOCROOT  . 'widgets/views/' . $renderFile . '.php',
            TS_DOCROOT . 'widgets/views/' . $this->_getClassName() . '/' . $renderFile . '.php',
            get_template_directory() . '/plugin_templates/' . $renderFile . '.php',
            TS_DOCROOT . '/templates/' . $renderFile . '.php'
        ];


        foreach ($templates as $template) {
            if (file_exists($template)) {
                return $template;
            }
        }

        wp_die("The view file does not exist: $renderFile");
    }

    /**
     * @return string
     *
     */
    protected function _getClassName()
    {

        $classPathParts = explode('\\', static::class);

        return mb_strtolower(
            array_pop($classPathParts),
            'UTF-8'
        );
    }

    /**
     * Init widget
     */
    protected function _init()
    {}

    /**
     * Verification of rights
     */
    protected function _access()
    {
        $rule = $this->rules();
        if (empty($rule)) {
            return true;
        }

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

    protected function _loadInlineScripts()
    {

    }
}