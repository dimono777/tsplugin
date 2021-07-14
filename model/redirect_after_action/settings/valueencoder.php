<?php

namespace tradersoft\model\redirect_after_action\settings;

/**
 * Class ValueEncoder
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 */
class ValueEncoder
{
    /** @var string */
    private static $_delimiter = '.';

    /**
     * Encode settings value
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param array $value
     * @return string
     */
    public static function encode(array $value)
    {
        return implode(self::$_delimiter, $value);
    }

    /**
     * Decode settings value
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param string $value
     * @return array $result
     */
    public static function decode($value)
    {
        $result = [
            'project' => 0,
            'page' => 0,
        ];

        if (!is_string($value) || !trim($value)) {
            return $result;
        }

        list($result['project'], $result['page']) = explode(self::$_delimiter, $value);

        return $result;
    }
}