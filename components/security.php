<?php

namespace tradersoft\components;

/**
 * Class Security
 *
 * Generates and checks signatures.
 *
 * @author  mykyta.popov <mykyta.popov@tstechpro.com>
 * Date: 26-Sep-19
 * @package tradersoft\components
 */
class Security
{
    /**
     * @var string Secret to generate signature.
     */
    protected $_secret = 'r62VS7xwLAwZVwRkeG9OAm4bA57P9jHRgr1OB12b';

    /**
     * Security constructor.
     *
     * @param string|null $secret Secret to generate signature. Default value will be used on null.
     */
    public function __construct($secret = null)
    {
        if ($secret !== null) {
            $this->_secret = $secret;
        }
    }

    /**
     * Creates signature using specified data and secret.
     *
     * @author mykyta.popov <mykyta.popov@tstechpro.com>
     *
     * @param string $data The data to create signature.
     *
     * @return string
     */
    public function getSignature($data)
    {
        return md5(md5($data) . $this->_secret);
    }
}
