<?php

namespace tradersoft\helpers;

use tradersoft\helpers\abstracts\BasicForm;
use tradersoft\model\Model;
use tradersoft\model\ModelWithFieldInterface;

/**
 * Simple active form
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class SimpleForm extends BasicForm
{
    protected $_model;

    /**
     * Begins a widget.
     * For example to use:
     * ```php
     * Form::begin($model, [
     *      'method'=>'GET',
     *      'action'=>'/action',
     *      'htmlOptions'=>[
     *          'class'=>'form-class',
     *          'id'=>'form-id'
     *      ]
     * ]);
     * ```
     * @param Model $model
     * @param array $config
     * @return static
     */
    public static function begin(Model $model, $config = [])
    {
        /* @var $form static */
        $form = self::_begin($config);
        $form->_setModel($model);

        return $form;
    }

    public static function end()
    {
        /** @var $form static */
        $form = self::_end();
        return $form;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function run(){

        $this->_jsOnJQueryRady(
            sprintf(
                "jQuery('#%s').activeForm( %s, %s);",
                $this->htmlOptions['id'],
                Json::htmlEncode($this->_getClientOptions()),
                Json::htmlEncode($this->_getFormSettings())
            ),
            'ts-activeForm'
        );
    }

    /**
     * @inheritdoc
     * For example to use:
     * ```php
     * $form->field('attribute_name', [
     *      'model' => $model, //If you want to override model
     *      'options' => ['class'=>'class-name', 'id'=>'id-name'],
     *      'template' => "<div>Text</div><p>{label}\n{input}\n{error}</p><span>Text</span>" //If you want to override default template
     * ]);
     * ```
     * @return Field
     */
    public function field($attribute, $options = [])
    {
        if (isset($options['model'])) {
            if ($options['model'] instanceof Model) {
                $model = $options['model'];
            }
            unset($options['model']);
        }
        if (empty($model)) {
            $model = $this->_model;
        }

        return $this->createField($model, $attribute, $options);
    }

    /**
     * @inheritDoc
     */
    public function createField(ModelWithFieldInterface $model, $attribute, $options = [])
    {
        return new Field($model, $this, $attribute, $options);
    }

    /**
     * @param Model $model
     */
    protected function _setModel(Model $model)
    {
        $this->_model = $model;
    }
}