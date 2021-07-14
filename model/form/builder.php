<?php

namespace tradersoft\model\form;

use tradersoft\helpers\abstracts\BasicForm;
use tradersoft\helpers\Arr;
use tradersoft\helpers\Field;
use tradersoft\helpers\form\MultiForm;
use tradersoft\helpers\FormBlock;
use tradersoft\helpers\Html;
use tradersoft\helpers\system\Translate;
use tradersoft\model\form\blocks\BlockInterface;
use tradersoft\model\form\decorators\FormAdditionalParam;
use tradersoft\model\form\fields\ActiveField;
use tradersoft\model\form\fields\FieldInterface;

class Builder
{
    protected $_model;

    protected $_formOptions = [];

    protected $_formClassName = 'ts-form-element';

    protected $_formTitleHtmlOptions = ['class' => 'active-form-title'];

    protected $_systemMessageHtmlOptions = ['class' => 'system-message-block'];

    /**
     * @var BlockInterface
     */
    protected $_currentRenderingBlock;

    protected $_currentRenderingBlockIndex;

    protected $_globalRenderingBlockIndex;

    public function __construct(FormBuilderModelInterface $model, array $config = [])
    {
        $this->_configure($config);
        $this->_model = $model;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function build()
    {
        $content = '';

        $content .= $this->_renderTitle();
        $content .= $this->_renderSystemMessage();

        $form = MultiForm::begin($this->_getFormOptions());
        $content .= $form->formStart();
        $content .= $form->getPreLoaderBlock();
        $content .= $this->_renderForm($form);
        $content .= $form->formEnd();
        MultiForm::end();

        return $content;
    }

    /**
     * @param BasicForm $form
     *
     * @return string
     * @throws \Exception
     */
    protected function _renderForm(MultiForm $form)
    {
        $content = '';
        $this->_globalRenderingBlockIndex = 1;
        foreach ($this->_model->attributes() as $blockName) {
            $blocks = $this->_model->getAttributeValue($blockName);
            $currentIndex = 0;
            $content .= Html::beginTag(Html::TAG_DIV, ['class' => 'form-blocks']);
            foreach ($blocks as $index => $block) {
                $currentIndex++;
                $this->_currentRenderingBlock = $block;
                $this->_currentRenderingBlockIndex = $index;
                $content .= $this->_renderBlock($form, $block, count($blocks) == $currentIndex, $currentIndex);
                $this->_globalRenderingBlockIndex++;
            }
            $content .= Html::endTag(Html::TAG_DIV);
        }

        return $content;
    }

    /**
     * @param MultiForm      $form
     * @param BlockInterface $block
     * @param bool           $isLast
     * @param int            $currentIndex
     *
     * @return string
     * @throws \Exception
     */
    protected function _renderBlock(MultiForm $form, BlockInterface $block, $isLast = false, $currentIndex = 0)
    {
        $formBlock = $form->createBlock($this->_model, $block->getName(), $this->_getBlockOptions());
        foreach ($this->_getGroupedFields($block) as $groupedField) {
            $formBlock->beginGroup();
            foreach ($groupedField as $field) {
                /** @var FieldInterface $field */
                $formField = $formBlock->field($block, $field->name, $field->getWrapperOptions());
                $this->_setFieldView($formField, $field);
                $this->_setLabel($formField, $field);
                $this->_setTooltip($formField, $field);
                $this->_setDescription($formField, $field);
                $this->_setEvents($formField, $field);
                $this->_setAdditionalData($formField, $field);
            }
            $formBlock->endGroup();
        }

        return $formBlock->render();
    }

    /**
     * @param Field          $formField
     * @param FieldInterface $field
     */
    protected function _setFieldView(Field $formField, FieldInterface $field)
    {
        $inputOptions = $this->_getFieldInputOptions($field);
        switch ($field->view) {
            case FieldInterface::VIEW_RADIO_LIST:
                // not break;
            case FieldInterface::VIEW_RADIO_COMMENT_LIST:
                // not break;
            case FieldInterface::VIEW_DROP_DOWN:
                $formField->{$field->view}((array)$field->items, $inputOptions);
                break;
            case FieldInterface::VIEW_INVISIBLE_CAPTCHA:
                $formField->{$field->view}(
                    Arr::get($field->fieldAttributes, ActiveField::ATTRIBUTE_CAPTCHA_SITE_KEY),
                    $inputOptions
                );
                break;
            default:
                $formField->{$field->view}($inputOptions);
                break;
        }
    }

    /**
     * @param FieldInterface $field
     *
     * @return array
     */
    protected function _getFieldInputOptions(FieldInterface $field)
    {
        $inputOptions = $field->getInputHtmlAttributes();
        if (!isset($inputOptions[Html::OPTION_NAME])) {
            $inputOptions[Html::OPTION_NAME] = $this->_getFieldInputName($field);
        }
        if (empty($inputOptions[Html::OPTION_ID])) {
            $inputOptions[Html::OPTION_ID] = $this->_getFieldInputId($field);
        }

        return $inputOptions;
    }

    /**
     * @param FieldInterface $field
     *
     * @return string
     */
    protected function _getFieldInputName(FieldInterface $field)
    {
        $index = $this->_currentRenderingBlockIndex;
        $block = $this->_currentRenderingBlock;

        return $this->_model->getName() . "[{$block->getName()}][{$index}][{$field->name}]";
    }

    /**
     * @param FieldInterface $field
     *
     * @return string
     */
    protected function _getFieldInputId(FieldInterface $field)
    {
        $index = $this->_currentRenderingBlockIndex;
        $block = $this->_currentRenderingBlock;

        return strtolower("{$this->_model->getName()}-{$block->getName()}-{$index}-{$field->name}");
    }

    /**
     * @param Field          $formField
     * @param FieldInterface $field
     */
    protected function _setLabel(Field $formField, FieldInterface $field)
    {
        if (!is_null($field->label) && $field->label != '') {
            $formField->label($field->getAttributeLabel());
        }
    }

    /**
     * @param Field          $formField
     * @param FieldInterface $field
     */
    protected function _setTooltip(Field $formField, FieldInterface $field)
    {
        if (!is_null($field->tooltip) && $field->tooltip != '') {
            $formField->tooltip($field->getAttributeTooltip());
        }
    }

    /**
     * @param Field          $formField
     * @param FieldInterface $field
     */
    protected function _setDescription(Field $formField, FieldInterface $field)
    {
        if (!is_null($field->description) && $field->description != '') {
            $formField->description($field->getAttributeDescription());
        }
    }

    /**
     * @param Field          $formField
     * @param FieldInterface $field
     */
    protected function _setEvents(Field $formField, FieldInterface $field)
    {
        if ($field->hasEvents()) {
            $formField->addEvents($field->events);
        }
    }

    /**
     * @param Field          $formField
     * @param FieldInterface $field
     */
    protected function _setAdditionalData(Field $formField, FieldInterface $field)
    {
        if ($field->hasAdditionalData()) {
            $formField->addAdditionalData($field->getAdditionalData());
        }
    }

    /**
     * @param BlockInterface $block
     *
     * @return array
     */
    protected function _getGroupedFields(BlockInterface $block)
    {
        $data = [];
        foreach ($block->getFields() as $field) {
            $data[$field->group][] = $field;
        }

        return $data;
    }

    /**
     * @return array
     */
    protected function _getBlockOptions()
    {
        $options = $this->_currentRenderingBlock->getViewOptions();
        $options[FormBlock::OPTION_INDEX_BLOCK] = $this->_currentRenderingBlockIndex;

        return Html::addClassToOptions("form-block-item-$this->_globalRenderingBlockIndex", $options);
    }

    /**
     * @return array
     */
    protected function _getFormOptions()
    {
        $options = $this->_formOptions;
        $options['htmlOptions'] = $this->_prepareFormHtmlOptions();

        return $options;
    }

    /**
     * @return mixed
     */
    protected function _prepareFormHtmlOptions()
    {
        $formClasses = [
            $this->_formClassName,
            strtolower($this->_model->getName()),
        ];

        $options['class'] = implode(' ', $formClasses);

        return $options;
    }

    /**
     * Render system message block
     */
    protected function _renderSystemMessage()
    {
        $msg = $this->_model->hasSystemMessage() ? $this->_model->renderSystemMessages() : '';

        return $this->_renderStaticBlock($msg, Html::TAG_DIV, $this->_systemMessageHtmlOptions, false);
    }

    /**
     * @return string
     */
    protected function _renderTitle()
    {
        $title = $this->_model->getStructureData()->getSiteTitle();

        return $this->_renderStaticBlock($title, Html::TAG_DIV, $this->_formTitleHtmlOptions);
    }

    /**
     * @param       $text
     * @param       $htmlTagName
     * @param array $htmlTagOptions
     * @param bool  $isXssClearText
     *
     * @return string
     */
    protected function _renderStaticBlock($text, $htmlTagName, $htmlTagOptions = [], $isXssClearText = true)
    {
        $text = Translate::__($text);

        $str = '';
        $str .= Html::beginTag($htmlTagName, $htmlTagOptions);
        $str .= $isXssClearText ? htmlentities($text) : $text;
        $str .= Html::endTag($htmlTagName);

        return $str;
    }

    /**
     * Set properties
     *
     * @param $properties array
     */
    private function _configure(array $properties)
    {
        foreach ($properties as $name => $value) {
            $publicMethodName = 'set' . ucfirst($name);
            $protectedMethodName = '_' . $publicMethodName;

            if (method_exists($this, $publicMethodName)) {
                $this->$publicMethodName($value);
                continue;
            } elseif (method_exists($this, $protectedMethodName)) {
                $this->$protectedMethodName($value);
                continue;
            }
        }
    }
}