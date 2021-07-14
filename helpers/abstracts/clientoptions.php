<?php

namespace tradersoft\helpers\abstracts;

class ClientOptions
{
    protected $_options = [];
    protected $_blockIndex = 0;

    public function addBlock()
    {
        $this->_blockIndex++;
    }

    public function setBlockOptions(array $blockOptions)
    {
        $this->_options[$this->_blockIndex]['options'] = $blockOptions;
    }

    public function addFieldOptions(array $fieldOptions)
    {
        $this->_options[$this->_blockIndex]['fields'][] = $fieldOptions;
    }

    public function getOptions()
    {
        return $this->_options;
    }
}