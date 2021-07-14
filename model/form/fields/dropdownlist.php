<?php

namespace tradersoft\model\form\fields;

use tradersoft\helpers\Arr;

class DropDownList extends ActiveField
{
    const KEY_IS_NEED_TRANSLATE = 'isNeedTranslate';
    const OPTION_ITEMS_ALREADY_TRANSLATED = 'alreadyTranslated';

    protected function _initDefaultInputHtmlAttribute()
    {
        parent::_initDefaultInputHtmlAttribute();

        if ($prompt = $this->fieldAttributes[static::ATTRIBUTE_PLACEHOLDER]) {
            $this->_inputHtmlAttributes['prompt'] = $prompt;
        }

        $this->_inputHtmlAttributes[static::KEY_IS_NEED_TRANSLATE] = $this->_isNeedTranslate();
    }

    /**
     * @return bool
     */
    protected function _isNeedTranslate()
    {
        return !Arr::path(
            $this->fieldAttributes,
            ActiveField::ATTRIBUTE_OPTION_GETTER . '.' . static::OPTION_ITEMS_ALREADY_TRANSLATED,
            false
        );
    }
}