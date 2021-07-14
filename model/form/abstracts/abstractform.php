<?php

namespace tradersoft\model\form\abstracts;

use tradersoft\helpers\Arr;
use tradersoft\helpers\captcha\InvisibleRecaptcha;
use tradersoft\helpers\Config;
use tradersoft\helpers\form\MultiForm;
use tradersoft\helpers\multi_language\Multi_Language;
use tradersoft\helpers\system\Translate;
use tradersoft\model\form\blocks\BlockInterface;
use tradersoft\model\form\blocks\DefaultBlock;
use tradersoft\model\form\blocks\Factory;
use tradersoft\model\form\decorators\CRMStructureBlock;
use tradersoft\model\form\decorators\StructureInterface;
use tradersoft\model\form\fields\ActiveField;
use tradersoft\model\form\fields\InvisibleCaptcha;
use tradersoft\model\form\FormBuilderModelInterface;
use tradersoft\model\Media_Queue;
use tradersoft\model\validator\ValidationTrait;
use tradersoft\traits\ActiveInterlayer;
use tradersoft\traits\SystemMessage;

/**
 * Class AbstractForm
 *
 * @author  Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 * @package tradersoft\model\form
 *
 */
abstract class AbstractForm implements FormBuilderModelInterface
{
    use ActiveInterlayer;
    use ValidationTrait;
    use SystemMessage;

    protected $_id;

    protected $_structureData;

    protected $_shortCodeParams;

    protected $_redirectUrl;

    protected $_blocks = [];

    /**
     * @var BlockInterface[]
     */
    protected $_blocksInfo = [];

    protected $_errors = [];

    /**
     * AbstractForm constructor.
     *
     * @param StructureInterface $structureData   - Structure form data
     * @param array              $shortCodeParams - Short code params
     *
     * @throws \Exception
     */
    public function __construct(StructureInterface $structureData, array $shortCodeParams = [])
    {
        $this->_structureData = $structureData;
        $this->_shortCodeParams = $shortCodeParams;
        $this->_initBlocks();
    }

    /**
     * @inheritDoc
     */
    public function getStructureData()
    {
        return $this->_structureData;
    }

    /**
     * @inheritDoc
     */
    public function hasAttribute($attribute)
    {
        return !is_null($this->getBlocksByName($attribute));
    }

    /**
     * @return string|null
     */
    public function getFormType()
    {
        return $this->_structureData->getType();
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        $this->_errors = [];
        $isValid = true;
        foreach ($this->getBlocks() as $blockName => $blocks) {
            foreach ($blocks as $index => $block) {
                /** @var BlockInterface $block */
                if (!$block->validate()) {
                    $isValid = false;
                    $this->_errors[$blockName][$index] = $block->getErrors();
                }
            }
        }

        return $isValid;
    }

    /**
     * @inheritDoc
     */
    public function getValidators($attributeName = null)
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function attributes()
    {
        return array_keys($this->getBlocks());
    }

    /**
     * @param string $attribute
     *
     * @return BlockInterface[]
     */
    public function getAttributeValue($attribute)
    {
        if (!($blocks = $this->getBlocksByName($attribute))) {
            return [];
        }

        return $blocks;
    }

    /**
     * @inheritDoc
     */
    public function getAttributesValues()
    {
        $data = [];

        foreach ($this->attributes() as $blockName) {
            foreach ($this->getBlocksByName($blockName) as $index => $block) {
                $data[$block->getBlockId()][$index] = $block->getAttributesValues();
            }
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function setAttributeValue($attributeName, $value)
    {
        foreach ($value as $index => $blockData) {
            if (!($block = $this->getBlock($attributeName, $index))) {
                continue;
            }
            if (!is_array($blockData)) {
                continue;
            }
            $block->setIndex($index);
            $block->load([$attributeName => $blockData]);
        }
    }

    /**
     * @inheritDoc
     */
    public function getAttributeLabel($attribute)
    {
        return $attribute;
    }

    /**
     * @inheritDoc
     */
    public function load(array $data, $formName = null)
    {
        $formName = $formName ? : $this->getName();
        $formData = Arr::get($data, $formName);

        if (!empty($formData) && is_array($formData)) {
            $this->setAttributes($formData);

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function setAttributes(array $values)
    {
        foreach ($this->attributes() as $blockName) {
            if (($blocksData = Arr::get($values, $blockName)) && is_array($blocksData)) {
                $blockStructure = $this->_getBlockInfoByName($blockName);
                $currentRepeat = 1;
                $repeatCnt = (int)$blockStructure->getBlockAttribute(BlockInterface::BLOCK_ATTR_REPEAT_CNT);
                $blocksData = array_values($blocksData);
                foreach ($blocksData as $index => $blockData) {
                    $block = clone $blockStructure;
                    $block->setIndex($index);
                    $block->load([$blockName => $blockData]);
                    $this->_blocks[$block->getName()][$index] = $block;
                    $currentRepeat++;
                    if ($currentRepeat > $repeatCnt) {
                        break;
                    }
                }
            }
        }
    }

    /**
     * Returns an array of block types in which arrays of specific blocks lie
     * Example return data:
     * [
     *      'multiple' => [BlockInterface, BlockInterface]
     *      'another' => [BlockInterface]
     * ]
     *
     * @return array
     */
    public function getBlocks()
    {
        return $this->_blocks;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getFormType();
    }

    /**
     * @inheritDoc
     */
    public function getBlock($name, $index)
    {
        return Arr::path($this->getBlocks(), "$name.$index");
    }

    /**
     * @inheritDoc
     */
    public function getBlocksByName($blockName)
    {
        return Arr::get($this->getBlocks(), $blockName, []);
    }

    public function getBlocksByTypeId($typeId)
    {
        $result = [];
        foreach ($this->getBlocks() as $blocks) {
            foreach ($blocks as $block) {
                if ($block->getBlockTypeId() == $typeId) {
                    $result[] = $block;
                }
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function isNeedRedirect()
    {
        return !empty($this->_redirectUrl);
    }

    /**
     * @inheritDoc
     */
    public function getRedirectUrl()
    {
        return $this->_redirectUrl;
    }

    /**
     * @inheritDoc
     */
    public function setRedirectUrl($url)
    {
        $this->_redirectUrl = $url;
    }

    /**
     * @throws \Exception
     */
    protected function _initBlocks()
    {
        foreach ($this->_structureData->getBlocks() as $block) {
            $block = Factory::getBlock(
                $block->getBlockTypeId(),
                $this->_structureData->getBlockFields($block->getId()),
                $block
            );
            $this->_addBlock($block);
        }

        // TODO: Delete it after deploy
        if (!$this->_blocks) {
            $this->_initDefaultBlock();
        }

        // Add recaptcha if it is need
        if ($this->_structureData->isCaptchaEnable()) {
            $this->_addInvisibleRecaptchaToForm();
        }
    }

    /**
     * TODO: Delete it after deploy
     *
     * @throws \Exception
     */
    protected function _initDefaultBlock()
    {
        $block = Factory::getBlock(
            BlockInterface::BLOCK_ID_DEFAULT,
            $this->_structureData->getFields(),
            new CRMStructureBlock([])
        );
        $this->_addBlock($block);
    }

    protected function _addBlock(BlockInterface $block)
    {
        $blockName = $block->getName();
        if (!isset($this->_blocks[$blockName])) {
            $this->_blocks[$blockName] = [];
        }
        $index = count($this->_blocks[$blockName]);

        $block->setIndex($index);
        $block->setParentModel($this);
        $this->_blocks[$blockName][$index] = $block;
        $this->_blocksInfo[$blockName] = clone $block;
    }

    /**
     * @throws \Exception
     */
    protected function _addInvisibleRecaptchaToForm()
    {
        $invisibleRecaptcha = new InvisibleRecaptcha();
        if (!$invisibleRecaptcha->isAvailable()) {
            return;
        }

        $captchaField = new InvisibleCaptcha();
        $captchaField->name = 'invisibleCaptcha';
        $captchaField->view = ActiveField::VIEW_INVISIBLE_CAPTCHA;
        $captchaField->isEditable = true;
        $captchaField->group = 0;
        $captchaField->fieldAttributes = [
            ActiveField::ATTRIBUTE_CAPTCHA_SITE_KEY => $invisibleRecaptcha->getSiteKey(),
        ];
        $captchaField->validationRules = [
            [
                'name' => 'recaptcha',
                'params' => [],
            ],
        ];

        $block = new DefaultBlock();
        $block->addField($captchaField);
        $this->_addBlock($block);

        $language = Multi_Language::getInstance()->getCurrentLanguage();

        Media_Queue::getInstanceByInitiatorOnly(MultiForm::class)
            ->addScript(
                "https://www.google.com/recaptcha/api.js?render=explicit&hl=$language",
                null,
                null,
                'recaptcha',
                [
                    'ts-activeForm',
                ],
                true
            );
    }

    /**
     * @param $blockName
     *
     * @return BlockInterface
     */
    protected function _getBlockInfoByName($blockName)
    {
        return Arr::get($this->_blocksInfo, $blockName);
    }

    /**
     * @param $blockId
     *
     * @return BlockInterface|null
     */
    protected function _getBlockInfoById($blockId)
    {
        foreach ($this->_blocksInfo as $blockInfo) {
            if ($blockInfo->getBlockId() == $blockId) {
                return $blockInfo;
            }
        }

        return null;
    }

    /**
     * @param $validationErrors
     *
     * @return array
     */
    protected function _prepareValidationErrors($validationErrors)
    {
        if (empty($validationErrors)) {
            return [];
        }

        $errors = [];
        foreach ($validationErrors as $blockId => $blocksErrors) {
            foreach ($blocksErrors as $indexBlock => $blockErrors) {
                if (!($blockInfo = $this->_getBlockInfoById($blockId))) {
                    continue;
                }
                $blockName = $blockInfo->getName();
                if (!($block = $this->getBlock($blockName, (int)$indexBlock))) {
                    continue;
                }
                foreach ($blockErrors as $fieldName => $error) {
                    $error = Translate::__(
                        Config::get("crm_validation_errors.$error", $error),
                        [':attribute' => $block->getAttributeLabel($fieldName)]
                    );
                    $errors[$blockName][$indexBlock][$fieldName] = $error;
                    $block->addError($fieldName, $error);
                }
            }
        }

        return $errors;
    }

    /**
     * @return array
     */
    protected function _getShortCodeParams()
    {
        return $this->_shortCodeParams;
    }

}