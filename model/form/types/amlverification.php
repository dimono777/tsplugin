<?php

namespace tradersoft\model\form\types;

use TS_Functions;
use TSInit;
use tradersoft\helpers\Arr;
use tradersoft\interfaces\ISystemMessage;
use tradersoft\model\form\abstracts\AbstractForm;
use tradersoft\model\form\blocks\BlockInterface;
use tradersoft\model\form\decorators\StructureInterface;

class AMLVerification extends AbstractForm
{
    const FIELD_NAME_DECLARATION_BELIEF = 'declaration';
    const FIELD_NAME_CONSENT_TAX_AUTHORITIES = 'tax';

    const ADDITIONAL_KEY_SPECIFIC_LABELS = 'specificLabels';

    const AML_REFERRER_URL_KEY = 'amlReferrerUrl';

    /**
     * AMLVerification constructor.
     *
     * @param StructureInterface $structureData
     * @param array              $shortCodeParams
     *
     * @throws \Exception
     */
    public function __construct(StructureInterface $structureData, array $shortCodeParams = [])
    {
        parent::__construct($structureData, $shortCodeParams);
        $this->_setFlashReferrerUrl();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function save()
    {
        $result = $this->_send(
            'form-verification-aml',
            $this->getAttributesValues(),
            [
                static::ADDITIONAL_KEY_SPECIFIC_LABELS => $this->_getSpecificFieldsLabels(),
            ]
        );

        if (!$result->isSuccess()) {
            $this->addSystemMessage($result->getMessage(), ISystemMessage::SYSTEM_MESSAGE_WARNING);
            return false;
        }

        if ($redirectUrl = $this->_getFlashReferrerUrl()) {
            $this->setRedirectUrl($redirectUrl);
        }

        $this->addSystemMessage(TS_Functions::__('Success'), ISystemMessage::SYSTEM_MESSAGE_SUCCESS);

        return true;
    }

    protected function _getSpecificFieldsLabels()
    {
        $labels = [];
        if (!($commonBlocks = $this->getBlocksByTypeId(BlockInterface::BLOCK_ID_COMMON))) {
            return $labels;
        }

        foreach ($commonBlocks as $commonBlock) {
            $fields = $commonBlock->getFields();

            if ($field = Arr::get($fields, static::FIELD_NAME_CONSENT_TAX_AUTHORITIES)) {
                $labels[static::FIELD_NAME_CONSENT_TAX_AUTHORITIES] = $field->getAttributeLabel();
            }
            if ($field = Arr::get($fields, static::FIELD_NAME_DECLARATION_BELIEF)) {
                $labels[static::FIELD_NAME_DECLARATION_BELIEF] = $field->getAttributeLabel();
            }
        }

        return $labels;
    }

    /**
     * Set referrer url if exist
     */
    protected function _setFlashReferrerUrl()
    {
        if (!TSInit::$app->request->isPost) {
            if ($redirectUrl = TSInit::$app->request->getReferer()) {
                TSInit::$app->session->setFlash(self::AML_REFERRER_URL_KEY, $redirectUrl);
            }
        }
    }

    /**
     * Get referer url
     *
     * @return null|string
     */
    protected function _getFlashReferrerUrl()
    {
        return TSInit::$app->session->getFlash(self::AML_REFERRER_URL_KEY);
    }
}