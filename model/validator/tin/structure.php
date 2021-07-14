<?php

namespace tradersoft\model\validator\tin;

use tradersoft\helpers\Arr;

class Structure
{
    /** @var string */
    protected $_value;

    /** @var array */
    protected $_settings;

    /**
     * @param string $value
     * @param array $settings
     */
    public function __construct($value, array $settings)
    {
        $this->_value = $value;
        $this->_settings = $settings;
    }

    /**
     * Validate structure
     *
     * @return bool
     * @throws ValidationException
     */
    public function validate()
    {
        if (empty($this->_settings)) {
            return true;
        }

        $result = preg_match(Arr::get($this->_settings, 'pattern', ''), $this->_value);

        if ($result === false) {
            throw new ValidationException('Invalid structure regex pattern');
        }

        return (bool) $result;
    }
}