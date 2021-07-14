<?php

namespace tradersoft\model\form\fields;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Html;

class RadioCommentList extends ActiveField
{
    protected $_radioName = Html::OPTION_RCL_RADIO_NAME;

    protected $_radioCommentName = Html::OPTION_RCL_COMMENT_NAME;

    public function validateNot_Empty()
    {
        $radioValue = $this->_getValueRadio();
        $commentValue = $this->_getValueComment();
        if (!$this->_isNotEmpty($radioValue)) {
            return false;
        }
        if ($this->_isRequiredComment($radioValue)) {
            return $this->_isNotEmpty($commentValue);
        }

        return true;
    }

    /**
     * @param $value
     */
    protected function _setDefaultValue($value)
    {
        $this->_defaultValue[$this->_radioName] = Arr::get($value, $this->_radioName);
        $this->_defaultValue[$this->_radioCommentName] = Arr::get($value, $this->_radioCommentName);
    }

    /**
     * @return string|null
     */
    protected function _getValueRadio()
    {
        $value = Arr::get($this->getAttributeValue(), $this->_radioName);
        if (!is_null($value)) {
            $value = (int)$value;
        }

        return $value;
    }

    /**
     * @return string|null
     */
    protected function _getValueComment()
    {
        return Arr::get($this->getAttributeValue(), $this->_radioCommentName);
    }

    /**
     * @param $radioValue
     *
     * @return bool
     */
    protected function _isRequiredComment($radioValue)
    {
        return (bool)Arr::get($this->_getCommentOptions($radioValue), Html::OPTION_VISIBLE);
    }

    /**
     * @param $radioValue
     *
     * @return array|mixed
     */
    protected function _getCommentOptions($radioValue)
    {
        foreach ($this->items as $itemData) {
            if ($radioValue == $itemData[Html::OPTION_RADIO_ITEM_RADIO_OPTIONS][Html::OPTION_VALUE]) {
                return $itemData[Html::OPTION_RADIO_ITEM_COMMENT_OPTIONS];
            }
        };

        return [];
    }

    /**
     * @param $value
     *
     * @return bool
     */
    protected function _isNotEmpty($value)
    {
        return ($value === '0' || !empty($value));
    }
}