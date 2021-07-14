<?php

namespace tradersoft\model\validator;

use tradersoft\helpers\captcha\InvisibleRecaptcha;
use tradersoft\model\ModelWithFieldInterface;
use TSInit;
use tradersoft\helpers\system\Translate;

/**
 * MaxLength validator.
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class ReCaptcha extends AbstractValidator
{
    protected static $_msg = 'Suspicious activity has been detected. Please try again or contact support.';

    /**
     * Required validator
     *
     * @param ModelWithFieldInterface $model
     * @param $attribute string
     * @param $value     mixed
     * @param $param     mixed
     *
     * @throws \Exception
     */
    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param)
    {
        if (!static::_checkScalar($model, $attribute, $value)) {
            return;
        }

        $invisibleRecapture = new InvisibleRecaptcha();
        if (!$invisibleRecapture->isAvailable()) {
            return;
        }

        if (!$invisibleRecapture->verifyResponse(TSInit::$app->request->getUserIP(), $value)) {
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
     *
     * @param ModelWithFieldInterface $model
     * @param $attribute string
     * @param $param     mixed
     *
     * @return string
     * @throws \Exception
     */
    public static function jsValidate(ModelWithFieldInterface $model, $attribute, $param)
    {
        $options = [
            'message' => Translate::__(
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute)
            ),
        ];

        return 'validation.recaptcha(value, messages, ' . json_encode(
                $options,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            ) . ');';
    }
}