<?php
/**
 * @link http://www.yiiframework.com/
 */

namespace tradersoft\helpers;

/**
 * Usage example:
 *
 * ```php
 * $array1 = [
 *     'ids' => [
 *         1,
 *     ],
 *     'validDomains' => [
 *         'example.com',
 *         'www.example.com',
 *     ],
 * ];
 *
 * $array2 = [
 *     'ids' => [
 *         2,
 *     ],
 *     'validDomains' => new \tradersoft\helpers\ArrayReplace([
 *         'site.com',
 *         'www.site.com',
 *     ]),
 * ];
 *
 * $result = \tradersoft\helpers\Arr::merge($array1, $array2);
 * ```
 *
 * The result will be
 *
 * ```php
 * [
 *     'ids' => [
 *         1,
 *         2,
 *     ],
 *     'validDomains' => [
 *         'site.com',
 *         'www.site.com',
 *     ],
 * ]
 * ```
 */
class ArrayReplace
{
    /**
     * @var mixed value used as replacement.
     */
    public $value;


    /**
     * Constructor.
     * @param mixed $value value used as replacement.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }
}
