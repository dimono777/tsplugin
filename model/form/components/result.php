<?php
namespace tradersoft\model\form\components;

/**
 * Class Result
 * @package tradersoft\model\form\components
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
class Result
{
    const RESULT_SUCCESS = 0;
    const RESULT_UNKNOWN_ERROR = 100;

    protected $_code;
    protected $_message;
    protected $_data;
    protected $_errors;

    /**
     * @param $code int
     * @param $message string
     * @param $data array
     * @param $errors array
     * @return static
     */
    public static function getResult($code, $message = '', array $data = [], array $errors = null)
    {
        $result = new static();
        $result->_setCode($code);
        $result->_message  = $message;
        $result->_data     = $data;
        $result->_errors   = $errors;

        return $result;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->_code == self::RESULT_SUCCESS;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->_errors);
    }

    /**
     * @return bool
     */
    public function hasData()
    {
        return !empty($this->_data);
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @return array|null
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * @param int $code
     */
    protected function _setCode($code)
    {
        $this->_code = (int)$code;
    }
}