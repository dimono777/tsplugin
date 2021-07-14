<?php

namespace tradersoft\helpers\form;

use tradersoft\helpers\abstracts\BasicForm;
use tradersoft\helpers\abstracts\ClientOptions;
use tradersoft\helpers\FormBlock;
use tradersoft\helpers\Json;
use tradersoft\model\ModelWithBlockInterface;
use tradersoft\model\ModelWithFieldInterface;

class MultiForm extends BasicForm
{
    /**
     * @var ClientOptions
     */
    protected $_clientOptions;

    public static function begin(array $config = [])
    {
        return self::_begin($config);
    }

    public static function end()
    {
        return self::_end();
    }
    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function run(){

        $this->_jsOnJQueryRady(
            sprintf(
                "jQuery('#%s').activeMultiForm( %s, %s);",
                $this->htmlOptions['id'],
                Json::htmlEncode($this->_getClientOptions()),
                Json::htmlEncode($this->_getFormSettings())
            ),
            'ts-activeForm'
        );
    }

    /**
     * Init form
     */
    public function init()
    {
        $this->_clientOptions = new ClientOptions();
    }

    /**
     * @param array $clientOption
     */
    public function addClientOption(array $clientOption)
    {
        $this->_clientOptions->addFieldOptions($clientOption);
    }

    /**
     * @param array $clientOption
     */
    public function setBlockOption(array $clientOption)
    {
        $this->_clientOptions->setBlockOptions($clientOption);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function createField(ModelWithFieldInterface $model, $attribute, $options = [])
    {
        throw new \Exception('The field must belong to a block');
    }

    /**
     * @param ModelWithBlockInterface $model
     * @param string                  $name
     * @param array                   $options
     *
     * @return FormBlock
     * @throws \Exception
     */
    public function createBlock(ModelWithBlockInterface $model, $name, array $options)
    {
        $this->_clientOptions->addBlock();

        return new FormBlock($model, $this, $name, $options);
    }

    /**
     * @return array
     */
    protected function _getClientOptions()
    {
        return $this->_clientOptions->getOptions();
    }
}