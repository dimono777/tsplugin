<?php

namespace tradersoft\model\validator;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Json;
use tradersoft\model\ModelWithFieldInterface;
use tradersoft\helpers\system\Translate;

class PersonalDataFormat extends Match
{
    private static $_patternsList = [];

    public static function validate(ModelWithFieldInterface $model, $attribute, $value, $param)
    {
        static::_init($param);

        foreach (static::$_patternsList as $pattern) {
            parent::validate(
                $model,
                $attribute,
                $value,
                [
                    'pattern' => $pattern,
                    'skipOnEmpty' => Arr::get($param, 'skipOnEmpty', static::$_skipOnEmpty),
                ]
            );
        }
    }

    public static function jsValidate(ModelWithFieldInterface $model, $attribute, $param)
    {
        static::_init($param);

        $jsPatternsList = [];
        foreach (static::$_patternsList as $pattern) {
            $jsPatternsList[] = Match::preparePatternForJs($pattern);
        }

        $options = [
            'patternsList' => $jsPatternsList,
            'message' => Translate::__(
                static::_getMessage($param),
                static::_getMessageVariables($model, $attribute)
            ),
        ];

        return 'validation.personalDataFormat(value, messages, ' . Json::htmlEncode($options) . ');';
    }

    /**
     * @param $param
     */
    protected static function _init($param)
    {
        if (!empty($param['patternsList'])) {
            static::$_patternsList = (array) $param['patternsList'];
        }
    }
}