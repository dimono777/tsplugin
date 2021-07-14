<?php

namespace tradersoft\helpers;

use tradersoft\helpers\form\MultiForm;
use tradersoft\helpers\system\Translate;
use tradersoft\model\form\blocks\BlockInterface;
use tradersoft\model\ModelWithBlockInterface;
use tradersoft\model\ModelWithFieldInterface;

class FormBlock
{
    const KEY_GROUP_FIELDS = 'fields';
    const KEY_GROUP_OPTIONS = 'options';

    const OPTION_INDEX_BLOCK = 'indexBlock';

    const JS_OPTION_NAME = 'name';
    const JS_OPTION_INDEX = 'index';
    const JS_OPTION_CONTAINER = 'container';
    const JS_OPTION_REPEAT_CNT = 'repeatCnt';
    const JS_OPTION_REPEAT_BTN_CLASS = 'repeatBtnClass';
    const JS_OPTION_REPEAT_BTN_TITLE = 'repeatBtnTitle';
    const JS_OPTION_REPEAT_TEMPLATE = 'repeatTemplate';

    protected $_model;
    protected $_form;
    protected $_block;
    protected $_options;
    protected $_currentGroup;
    protected $_groups = [];
    protected $_duplicateField;
    protected $_duplicateFieldClass = 'block-duplicate';
    protected $_blockClassName = 'form-item-block';
    protected $_groupClassName = 'form-group';

    /**
     * FormBlock constructor.
     *
     * @param ModelWithBlockInterface $model
     * @param MultiForm               $form
     * @param string                  $blockName
     * @param array                   $options
     *
     * @throws \Exception
     */
    public function __construct(ModelWithBlockInterface $model, MultiForm $form, $blockName, array $options)
    {
        $this->_model = $model;
        $this->_form = $form;
        $this->_options = $options;

        $indexBlock = Arr::remove($options, static::OPTION_INDEX_BLOCK);
        if (!($block = $model->getBlock($blockName, $indexBlock))) {
            throw new \Exception("Unknown block. [name=$blockName, index=$indexBlock]");
        }
        $this->_block = $block;
    }

    /**
     * @param ModelWithFieldInterface $model
     * @param string                  $attribute
     * @param array                   $options
     *
     * @return Field
     */
    public function field(ModelWithFieldInterface $model, $attribute, array $options)
    {
        $field = new Field($model, $this->_form, $attribute, $options);
        $this->addField($field);

        return $field;
    }

    /**
     * @param string $title
     * @param array  $options
     */
    public function duplicateInput($title, array $options = [])
    {
        $title = Translate::__($title);
        $options = Html::addClassToOptions($this->_duplicateFieldClass, $options);
        $this->_duplicateField = Html::tag(Html::TAG_DIV, Html::tag(Html::TAG_SPAN, $title, $options));
    }

    /**
     * @param Field $field
     */
    public function addField(Field $field)
    {
        $this->_addFieldToGroup($field);
    }

    /**
     * @param array $groupOptions
     */
    public function beginGroup(array $groupOptions = [])
    {
        if ($this->_isGroupStarted()) {
            $this->endGroup();
        }
        $groupOptions = Html::addClassToOptions($this->_groupClassName, $groupOptions);

        $this->_currentGroup = [static::KEY_GROUP_OPTIONS => $groupOptions, static::KEY_GROUP_FIELDS => []];
    }

    public function endGroup()
    {
        if (!$this->_isGroupStarted()) {
            return;
        }
        $this->_groups[] = $this->_currentGroup;
        $this->_currentGroup = null;
    }

    /**
     * @return string
     */
    public function render()
    {
        $content = '';
        $this->endGroup();
        foreach ($this->_getGroups() as $group) {
            $content .= $this->_renderGroup($group);
        }
        $content = $this->_beginBlock() . $content . $this->_endBlock();

        $this->_form->setBlockOption($this->_getBlockOptions($content));

        return $content . $this->_renderDuplicateField();
    }

    /**
     * @param array $group
     *
     * @return string
     */
    protected function _renderGroup(array $group)
    {
        $content = '';

        foreach ($this->_getGroupFields($group) as $field) {
            $content .= $field->render();
        }

        return Html::tag(Html::TAG_DIV, $content, $this->_getGroupOptions($group));
    }

    /**
     * @param Field $field
     */
    protected function _addFieldToGroup(Field $field)
    {
        if (!$this->_isGroupStarted()) {
            $this->beginGroup();
        }
        $this->_currentGroup[static::KEY_GROUP_FIELDS][] = $field;
    }

    /**
     * @return bool
     */
    protected function _isGroupStarted()
    {
        return !is_null($this->_currentGroup);
    }

    /**
     * @return array
     */
    protected function _getGroups()
    {
        return $this->_groups;
    }

    /**
     * @param array $group
     *
     * @return Field[]
     */
    protected function _getGroupFields(array $group)
    {
        return $group[static::KEY_GROUP_FIELDS];
    }

    /**
     * @param array $group
     *
     * @return Field[]
     */
    protected function _getGroupOptions(array $group)
    {
        return $group[static::KEY_GROUP_OPTIONS];
    }

    /**
     * @return string
     */
    protected function _beginBlock()
    {
        return Html::beginTag(HTML::TAG_DIV, $this->_getBlockHtmlOptions());
    }

    /**
     * @return string
     */
    protected function _endBlock()
    {
        return Html::endTag(HTML::TAG_DIV);
    }

    /**
     * @return string
     */
    protected function _renderDuplicateField()
    {
        if (!$this->_duplicateField) {
            return '';
        }

        return $this->_duplicateField;
    }

    /**
     * @return array
     */
    protected function _getBlockHtmlOptions()
    {
        $options[Html::OPTION_CLASS] = Arr::get($this->_options, Html::OPTION_CLASS);
        $options = Html::addClassToOptions("block-id-{$this->_block->formName()}", $options);
        $options = Html::addClassToOptions($this->_blockClassName, $options);

        return $options;
    }

    /**
     * @param $renderingBlock
     *
     * @return array
     */
    protected function _getBlockOptions($renderingBlock)
    {
        $classes = explode(' ', Arr::get($this->_getBlockHtmlOptions(), Html::OPTION_CLASS, ''));
        $repeatCnt = (int)$this->_block->getBlockAttribute(BlockInterface::BLOCK_ATTR_REPEAT_CNT);
        $repeatTitle = $this->_block->getBlockAttribute(BlockInterface::BLOCK_ATTR_REPEAT_BTN_TITLE);

        $options = [
            static::JS_OPTION_NAME => $this->_block->getName(),
            static::JS_OPTION_INDEX => $this->_block->getIndex(),
            static::JS_OPTION_CONTAINER => '.' . implode('.', $classes),
            static::JS_OPTION_REPEAT_CNT => $repeatCnt,
        ];

        if ($repeatCnt) {
            $options[static::JS_OPTION_REPEAT_BTN_CLASS] = $this->_duplicateFieldClass;
            $options[static::JS_OPTION_REPEAT_TEMPLATE] = $renderingBlock;
            $options[static::JS_OPTION_REPEAT_BTN_TITLE] = $repeatTitle;
        }

        return $options;
    }
}