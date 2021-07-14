<?php

namespace tradersoft\model\validator\tin;

use tradersoft\helpers\Arr;

class Formatter
{
    const FILTER_REMOVE_SPECIAL_CHARS = 'removeSpecialChars';
    const FILTER_PREPEND = 'prepend';

    const DEFAULT_SETTINGS = [
        self::FILTER_REMOVE_SPECIAL_CHARS,
    ];

    const EXCLUDED_SPECIAL_CHARS_LIST = '!\'?"№%:,.;()_+-=  @#$^&*\\|/`~{}[]><±';

    protected $_settings = [];

    /**
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        if (empty($settings)) {
            $settings = self::DEFAULT_SETTINGS;
        }

        $this->_settings = $settings;
    }

    /**
     * Filter value by given settings
     *
     * @param string $value
     * @return string
     * @throws ValidationException
     */
    public function filter($value)
    {
        foreach ($this->_settings as $method => $params) {
            if (!is_array($params)) {
                $method = $params;
                $params = [];
            }

            if (!method_exists($this, $method)) {
                throw new ValidationException("Invalid formatter name '$method'");
            }

            $value = call_user_func([$this, $method], $value, $params);
        }

        return $value;
    }

    /**
     * @param string $value
     * @param array $params
     * @return string
     * @throws ValidationException
     */
    public function removeSpecialChars($value, array $params)
    {
        $excludedSpecialChars = preg_quote(static::EXCLUDED_SPECIAL_CHARS_LIST, '/');
        $allowedSpecialChars = preg_quote(Arr::get($params, 'allowed', ''), '/');
        if ($allowedSpecialChars) {
            $allowedSpecialChars = '(?![' . $allowedSpecialChars . '])';
        }
        $pattern = "/{$allowedSpecialChars}[$excludedSpecialChars]/u";

        $value = preg_replace($pattern, '', $value);

        if (is_null($value)) {
            throw new ValidationException("Invalid filtering pattern $pattern");
        }

        return $value;
    }

    /**
     * Remove special chars from value
     *
     * @param string $value
     * @param array $params
     * @return string
     */
    protected function prepend($value, array $params)
    {
        $fillToLength = Arr::get($params, 'length', 0);
        if ($fillToLength > 0) {
            $limit = Arr::get($params, 'limit', 0);
            if ($limit == 0 || ($limit + strlen($value)) >= $fillToLength) {
                $value = str_pad($value, $fillToLength, Arr::get($params, 'fillBy', '0'), STR_PAD_LEFT);
            }
        }

        return $value;
    }
}