<?php
namespace tradersoft\model\validator;

use tradersoft\helpers\Arr;
use tradersoft\helpers\system\Translate;
use tradersoft\model\ModelWithFieldInterface;

/**
 * Compare validator.
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Compare extends AbstractValidator
{
    const TYPE_NUMBER = 'number';
    const VARIABLE_KEY_OPERATOR = '[OPERATOR]';
    const VARIABLE_KEY_COMPARED_FIELD_LABEL = '[COMPARED_FIELD_LABEL]';

    protected static $_msg = self::VARIABLE_KEY_FIELD_LABEL . ' must be ' .  self::VARIABLE_KEY_OPERATOR . ' ' . self::VARIABLE_KEY_COMPARED_FIELD_LABEL;
    protected static $_msgOperator;
    protected static $_msgCompareAttribute;

    private static $_skipOnEmpty = true;
    private static $_operators = [
        '==', '!=', '>', '<', '>=', '<='
    ];

    /**
     * Required validator
     * @param ModelWithFieldInterface $model
     * @param $attribute string
     * @param $value mixed
     * @param $param mixed
     * @throws \Exception
     */
    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param)
    {
        if (!static::_checkScalar($model, $attribute, $value)) {
            return;
        }

        static::_init($model, $attribute, $param);
        if (static::$_skipOnEmpty && empty($value)) {
            return;
        }
        if (isset($param['compareValue'])) {
            $cValue = $param['compareValue'];
        } else {
            $cValue = static::_getCompareAttributeValue($model, $param['compareAttribute']);
        }
        if (!static::_compareValue($param['operator'], $value, $cValue, Arr::get($param, 'type', 'string'))) {
            static::_addError(
                $model,
                $attribute,
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute)
            );
        }
    }

    /**
     * Required validator
     * @param ModelWithFieldInterface $model
     * @param $attribute string
     * @param $param mixed
     * @return string
     * @throws \Exception
     */
    public static function jsValidate(ModelWithFieldInterface $model, $attribute, $param)
    {
        static::_init($model, $attribute, $param);
        $options = [
            'skipOnEmpty' => (int)static::$_skipOnEmpty,
            'operator' => Arr::get($param, 'operator'),
            'compareAttribute' => Arr::get($param, 'compareAttribute'),
            'compareAttributeBlock' => null,
            'compareValue' => Arr::get($param, 'compareValue'),
            'message' => Translate::__(
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute)
            ),
            'type' => Arr::get($param, 'type', 'string'),
            'atribute' => $attribute,
            'formName' => $model->formName(),
        ];
        if ($compareModel = static::_getCompareAttributeModel($model, $param['compareAttribute'])) {
            $options['compareAttributeBlock'] = $compareModel->formName();
        }
        $options = json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return "validation.compare(attribute, value, messages, $options, field);";
    }

    /**
     * @param $model ModelWithFieldInterface
     * @param $attribute string
     * @param $param array
     * @throws \Exception
     */
    protected static function _init(ModelWithFieldInterface $model, $attribute, $param)
    {
        if (isset($param['skipOnEmpty'])) {
            static::$_skipOnEmpty = $param['skipOnEmpty'];
        }
        if (empty($param['operator'])) {
            throw new \Exception(\TS_Functions::__('Operator must be set'));
        }
        if (!in_array($param['operator'], static::$_operators)) {
            throw new \Exception(\TS_Functions::__('Unknown operator: ') . $param['operator']);
        }
        if (!isset($param['compareValue']) && !isset($param['compareAttribute'])) {
            throw new \Exception(\TS_Functions::__('compare value or compare attribute must be set'));
        }

        static::_prepareErrorMessage($model, $param);
    }

    /**
     * @param ModelWithFieldInterface $model
     * @param       $param
     */
    protected static function _prepareErrorMessage(ModelWithFieldInterface $model, $param)
    {
        if (isset($param['compareValue'])) {
            static::$_msgCompareAttribute = $param['compareValue'];
        } else {
            if ($compareModel = static::_getCompareAttributeModel($model, $param['compareAttribute'])) {
                static::$_msgCompareAttribute = $compareModel->getAttributeLabel($param['compareAttribute']);
            }
        }

        switch ($param['operator']) {
            case '==':
                static::$_msgOperator = Translate::__('equal to');
                break;
            case '!=':
                static::$_msgOperator = Translate::__('not equal to');
                break;
            case '>':
                static::$_msgOperator = Translate::__('greater than');
                break;
            case '>=':
                static::$_msgOperator = Translate::__(' greater than or equal to');
                break;
            case '<':
                static::$_msgOperator = Translate::__('less than');
                break;
            case '<=':
                static::$_msgOperator = Translate::__('less than or equal to');
                break;
        }
    }

    /**
     * @inheritdoc
     */
    protected static function _getMessageVariables(ModelWithFieldInterface $model, $attribute)
    {
        return parent::_getMessageVariables($model, $attribute) + [
            static::VARIABLE_KEY_OPERATOR => Translate::__(static::$_msgOperator),
            static::VARIABLE_KEY_COMPARED_FIELD_LABEL => Translate::__(static::$_msgCompareAttribute),
        ];
    }

    /**
     * @param $operator string
     * @param $value mixed
     * @param $cValue mixed
     * @param $type string
     * @return bool
     */
    protected static function _compareValue($operator, $value, $cValue, $type)
    {
        if ($type === static::TYPE_NUMBER) {
            $value = (float) $value;
            $cValue = (float) $cValue;
        } else {
            $value = (string) $value;
            $cValue = (string) $cValue;
        }
        switch ($operator) {
            case '==':
                return $value == $cValue;
            case '!=':
                return $value != $cValue;
            case '>':
                return $value > $cValue;
            case '>=':
                return $value >= $cValue;
            case '<':
                return $value < $cValue;
            case '<=':
                return $value <= $cValue;
            default:
                return false;
        }
    }

    protected static function _getCompareAttributeValue(ModelWithFieldInterface $model, $attrName)
    {
        $compareModel = static::_getCompareAttributeModel($model, $attrName);
        if (is_null($compareModel)) {
            return null;
        }

        return $compareModel->getAttributeValue($attrName);
    }

    protected static function _getCompareAttributeModel(ModelWithFieldInterface $model, $attrName)
    {
        if (in_array($attrName, $model->attributes())) {
            return $model;
        }
        foreach ($model->getRelationModels() as $relationModel) {
            if (in_array($attrName, $relationModel->attributes())) {
                return $relationModel;
            }
        }
        return null;
    }
}