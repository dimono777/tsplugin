<?php

namespace tradersoft\helpers;

use TSInit;

/**
 * Class Currency - used to work with currencies in the plugin
 *
 * @author Alexander Shpak <alexander.shpak@tstechpro.com>
 */
class Currency
{
    protected static $_instance;

    /**
     * @var array $_properties
     */
    protected $_properties = [];

    /**
     * @var bool $_isInited
     */
    protected $_isInited = false;

    /**
     * Get Currency instance
     *
     * @author Alexander Shpak <alexander.shpak@tstechpro.com>
     * @return static
     *
     */
    public static function getInstance()
    {
        if (is_null(static::$_instance)) {
            $_currencyInstance = new static();
            if ($_currencyInstance->_isInited) {
                static::$_instance = $_currencyInstance;
            } else {
                return $_currencyInstance;
            }
        }

        return static::$_instance;
    }

    /**
     * Get currency code
     *
     * @author Alexander Shpak <alexander.shpak@tstechpro.com>
     * @return string
     *
     */
    public function getCode()
    {

        return Arr::get($this->_properties, 'code', '');
    }

    /**
     * Get currency symbol
     *
     * @author Alexander Shpak <alexander.shpak@tstechpro.com>
     * @return string
     *
     */
    public function getSymbol()
    {

        return Arr::get($this->_properties, 'symbol', '');
    }

    /**
     * Get currency precision
     *
     * @author Alexander Shpak <alexander.shpak@tstechpro.com>
     * @return string
     *
     */
    public function getPrecision()
    {

        return Arr::get($this->_properties, 'precision', 0);
    }

    /**
     * Format money value according to currency precision
     *
     * @author Alexander Shpak <alexander.shpak@tstechpro.com>
     *
     * @param string $value
     *
     * @return string
     *
     */
    public function formatValue($value)
    {

        return number_format(
            $value,
            $this->getPrecision(),
            '.',
            ''
        );
    }

    /**
     * Format amount according to currency precision and add currency symbol to it
     *
     * @author Alexander Shpak <alexander.shpak@tstechpro.com>
     *
     * @param string $amount
     *
     * @return string
     *
     */
    public function renderAmount($amount)
    {

        return $this->getSymbol() . $this->formatValue($amount);
    }

    /**
     * Constructor
     *
     * @author Alexander Shpak <alexander.shpak@tstechpro.com>
     */
    protected function __construct()
    {

        $trader = TSInit::$app->trader;
        if (!$trader->isGuest) {
            $this->_properties = [
                'code' => $trader->get('currency'),
                'symbol' => $trader->get('currencySymbol'),
                'precision' => $trader->get('currencyPrecision', 2),
            ];
            $this->_isInited = true;
        }
    }
}