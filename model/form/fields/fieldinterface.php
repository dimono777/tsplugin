<?php

namespace tradersoft\model\form\fields;

use tradersoft\helpers\Field;

interface FieldInterface
{
    const VIEW_TEXT_INPUT = 'textInput';
    const VIEW_HIDDEN_INPUT = 'hiddenInput';
    const VIEW_PASSWORD_INPUT = 'passwordInput';
    const VIEW_FILE_INPUT = 'fileInput';
    const VIEW_TEXTAREA = 'textarea';
    const VIEW_RADIO = 'radio';
    const VIEW_RADIO_LIST = 'radioList';
    const VIEW_DROP_DOWN = 'dropDownList';
    const VIEW_CHECKBOX = 'checkbox';
    const VIEW_SUBMIT_INPUT = 'submitInput';
    const VIEW_DOUBLER_PHONE_INPUT = 'doublePhoneInput';
    const VIEW_INVISIBLE_CAPTCHA = Field::TYPE_INVISIBLE_CAPTCHA;
    const VIEW_RADIO_COMMENT_LIST = Field::TYPE_RADIO_COMMENT_LIST;


    /**
     * @param array       $data
     * @param string|null $formName
     *
     * @return bool
     */
    public function load(array $data, $formName = null);

    /**
     * @return mixed
     */
    public function getAttributeValue();

    /**
     * @return string
     */
    public function getAttributeLabel();

    /**
     * @return string
     */
    public function getAttributeTooltip();

    /**
     * @return string
     */
    public function getAttributeDescription();

    /**
     * @return array|null
     */
    public function getAttributeRules();

    /**
     * @return array
     */
    public function getWrapperOptions();

    /**
     * @return array
     */
    public function getInputHtmlAttributes();

    /**
     * @return bool
     */
    public function hasEvents();

    /**
     * @return bool
     */
    public function hasAdditionalData();

    /**
     * @return array
     */
    public function getAdditionalData();
}