<?php
namespace tradersoft\model\validator;

use tradersoft\helpers\ExternalFormValidationRule;
use tradersoft\model\ModelWithFieldInterface;

/**
 * Class Phone
 * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
 */
class Phone implements Validator
{
    protected static $_defaultPattern = '/^\+?[*_0-9\s\-\(\)\/\.]*\d+[*_0-9\s\-\(\)\/\.]*\d+[*_0-9\s\-\(\)\/\.]*\d+$/';

    /**
     * @inheritDoc
     */
    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param)
    {
        $skipOnEmpty = !empty($param['skipOnEmpty']);
        if ($skipOnEmpty && empty($value)) {
            return true;
        }

        $totalString = implode('', array_merge(static::_getFields($param), [$value]));
        foreach (static::_getValidators() as $validator => $validatorParams) {
            /** @var $validator Validator */
            $validatorParams['skipOnEmpty'] = $skipOnEmpty;
            $validator::validate($model, $attribute, $totalString, $validatorParams);

            if ($model->hasErrors($attribute)) {
                foreach (static::_getFields($param) as $anotherField => $value) {
                    $model->addError($anotherField);
                }

                return false;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public static function jsValidate(ModelWithFieldInterface $model, $attribute, $param)
    {
        return MaxLength::jsValidate(
            $model,
            $attribute,
            ['max' => static::_getValidators()[MaxLength::class]['max']]
        );
    }

    /**
     * Getting phone validators
     *
     * @return array
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    protected static function _getValidators()
    {
        return [
            MaxLength::class => [
                'max' => 30,
            ],

            Match::class => [
                'pattern' => ExternalFormValidationRule::getFieldRuleParams(
                    ExternalFormValidationRule::FIELD_PHONE,
                    ExternalFormValidationRule::VALIDATOR_PATTERN,
                    static::$_defaultPattern
                ),
            ],
        ];
    }

    /**
     * Getting additional fields
     *
     * @param array $param
     *
     * @return array
     * @author Alexander Penkov <alexandr.penkov@tstechpro.com>
     */
    protected static function _getFields($param)
    {
        return !empty($param['fields']) ? $param['fields'] : [];
    }
}