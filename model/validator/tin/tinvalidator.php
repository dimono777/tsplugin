<?php

namespace tradersoft\model\validator\tin;

use Exception;

class TINValidator
{
    /** @var string */
    protected $_filteredValue;

    /** @var Configuration */
    protected $_config;

    public function __construct(Configuration $config)
    {
        $this->_config = $config;
    }

    /**
     * @param string $value
     * @return bool
     * @throws Exception
     */
    public function validate($value)
    {
        try {
            if (is_null($value)) {
                return true;
            }

            $formatter = new Formatter($this->_config->formatter);
            $this->_filteredValue = $formatter->filter($value);

            $structure = new Structure($this->_filteredValue, $this->_config->structure);
            if (!$structure->validate()) {
                return false;
            }

        } catch (ValidationException $exception) {
            return false;
        }

        return true;
    }

    /**
     * Get filtered value after validation
     * (original value can be changed through validation process)
     *
     * @return string
     */
    public function getFilteredValue()
    {
        return $this->_filteredValue;
    }

}