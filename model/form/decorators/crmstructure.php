<?php

namespace tradersoft\model\form\decorators;

use tradersoft\helpers\Arr;

class CRMStructure implements StructureInterface
{
    protected $_structure;

    /**
     * @var FormAdditionalParam[]
     */
    protected $_additionalParams = [];
    protected $_blocks = [];

    const TYPE_REGISTRATION = 'Registration';
    const TYPE_AML_VERIFICATION = 'AMLVerification';
    const TYPE_EMAIL_FOR_PASSWORD_RECOVERY = 'EmailForPasswordRecovery';
    const TYPE_PASSWORD_RECOVERY = 'PasswordRecovery';
    const TYPE_CONTACT_US = 'ContactUs';

    const ADDITIONAL_PARAM_SITE_TITLE = 1;
    const ADDITIONAL_PARAM_DISABLE_DEFAULT_STYLES = 2;
    const ADDITIONAL_PARAM_IS_CAPTCHA_ENABLE = 3;

    /**
     * CRMStructure constructor.
     *
     * @param array $structure
     *
     * @throws \Exception
     */
    public function __construct(array $structure)
    {
        $this->_structure = $structure;
        $this->_initAdditionalParams();
        $this->_initBlocks();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int)$this->getAttribute(static::FIELD_ID);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getAttribute(static::FIELD_TYPE);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getAttribute(static::FIELD_NAME);
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->getAttribute(static::FIELD_FIELDS);
    }

    /**
     * @inheritDoc
     */
    public function getBlocks()
    {
        return array_values($this->_blocks);
    }

    /**
     * @inheritDoc
     */
    public function getBlock($blockId)
    {
        return Arr::get($this->_blocks, $blockId);
    }

    /**
     * @inheritDoc
     */
    public function getBlockFields($blockId)
    {
        $fields = [];
        foreach ($this->getFields() as $fieldData) {
            if ($blockId == $fieldData[CRMStructureFieldInterface::FIELD_BLOCK_ID]) {
                $fields[] = $fieldData;
            }
        }

        return $fields;
    }

    /**
     * @return FormAdditionalParam[]
     */
    public function getAdditionalParams()
    {
        return $this->_additionalParams;
    }

    /**
     * @param $paramId
     *
     * @return FormAdditionalParam|null
     */
    public function getAdditionalParam($paramId)
    {
        return Arr::get($this->_additionalParams, $paramId);
    }

    /**
     * @return string
     */
    public function getSiteTitle()
    {
        if (!($param = $this->_getAdditionalParamByFormType(static::ADDITIONAL_PARAM_SITE_TITLE))) {
            return '';
        }

        return $param->getValue();
    }

    /**
     * @return bool
     */
    public function isCaptchaEnable()
    {
        if (!($param = $this->_getAdditionalParamByFormType(static::ADDITIONAL_PARAM_IS_CAPTCHA_ENABLE))) {
            return false;
        }

        return (bool) $param->getValue();
    }

    /**
     * @inheritDoc
     */
    public function isDisableDefaultStyles()
    {
        if (!($param = $this->_getAdditionalParamByFormType(static::ADDITIONAL_PARAM_DISABLE_DEFAULT_STYLES))) {
            return false;
        }

        return (bool)$param->getValue();
    }

    /**
     * @param string $attributeName
     *
     * @return mixed
     */
    public function getAttribute($attributeName)
    {
        return Arr::get($this->_structure, $attributeName);
    }

    /**
     * @throws \Exception
     */
    protected function _initAdditionalParams()
    {
        foreach ($this->_structure[static::FIELD_ADDITIONAL_PARAMS] as $paramData) {
            $model = new FormAdditionalParam($paramData);
            $this->_additionalParams[$model->getParamId()] = $model;
        }
    }

    protected function _initBlocks()
    {
        if (isset($this->_structure[static::FIELD_BLOCKS])) {
            foreach ($this->_structure[static::FIELD_BLOCKS] as $block) {
                $model = new CRMStructureBlock($block);
                $this->_blocks[$model->getId()] = $model;
            }
        }
    }

    /**
     * @param $additionalParam
     *
     * @return FormAdditionalParam|null
     */
    protected function _getAdditionalParamByFormType($additionalParam)
    {
        $map = [
            static::TYPE_AML_VERIFICATION => [
                static::ADDITIONAL_PARAM_SITE_TITLE => FormAdditionalParam::PARAM_ID_AML_SITE_TITLE,
                static::ADDITIONAL_PARAM_DISABLE_DEFAULT_STYLES => FormAdditionalParam::PARAM_ID_AML_DISABLE_DEFAULT_STYLES,
            ],
            static::TYPE_REGISTRATION => [
                static::ADDITIONAL_PARAM_SITE_TITLE => FormAdditionalParam::PARAM_ID_REGISTRATION_SITE_TITLE,
                static::ADDITIONAL_PARAM_IS_CAPTCHA_ENABLE => FormAdditionalParam::PARAM_ID_WITH_CAPTCHA,
                static::ADDITIONAL_PARAM_DISABLE_DEFAULT_STYLES => FormAdditionalParam::PARAM_ID_REGISTRATION_DISABLE_DEFAULT_STYLES,
            ],
            static::TYPE_EMAIL_FOR_PASSWORD_RECOVERY => [
                static::ADDITIONAL_PARAM_SITE_TITLE => FormAdditionalParam::PARAM_ID_EMAIL_PASSWORD_RECOVERY_SITE_TITLE,
                static::ADDITIONAL_PARAM_IS_CAPTCHA_ENABLE => FormAdditionalParam::PARAM_ID_EMAIL_PASSWORD_RECOVERY_INVISIBLE_RECAPTCHA,
                static::ADDITIONAL_PARAM_DISABLE_DEFAULT_STYLES => FormAdditionalParam::PARAM_ID_EMAIL_PASSWORD_RECOVERY_DISABLE_DEFAULT_STYLES,
            ],
            static::TYPE_PASSWORD_RECOVERY => [
                static::ADDITIONAL_PARAM_SITE_TITLE => FormAdditionalParam::PARAM_ID_PASSWORD_RECOVERY_SITE_TITLE,
                static::ADDITIONAL_PARAM_DISABLE_DEFAULT_STYLES => FormAdditionalParam::PARAM_ID_PASSWORD_RECOVERY_DISABLE_DEFAULT_STYLES,
            ],
            static::TYPE_CONTACT_US => [
                static::ADDITIONAL_PARAM_SITE_TITLE => FormAdditionalParam::PARAM_ID_CONTACT_US_SITE_TITLE,
                static::ADDITIONAL_PARAM_IS_CAPTCHA_ENABLE => FormAdditionalParam::PARAM_ID_CONTACT_US_INVISIBLE_RECAPTCHA,
                static::ADDITIONAL_PARAM_DISABLE_DEFAULT_STYLES => FormAdditionalParam::PARAM_ID_CONTACT_US_DISABLE_DEFAULT_STYLES,
            ],
        ];

        if (!($paramId = Arr::path($map, "{$this->getType()}.{$additionalParam}"))) {
            return null;
        }

        return $this->getAdditionalParam($paramId);
    }
}