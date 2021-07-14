<?php

namespace tradersoft\model\validator;

use tradersoft\helpers\Arr;
use tradersoft\helpers\system\Translate;
use Exception;
use tradersoft\model\validator\prevalidations\PreValidator;

trait ValidationTrait
{
    protected $_errors = [];
    protected $_activeValidators = [];

    /**
     * @param string $attribute
     *
     * @return bool
     */
    abstract public function hasAttribute($attribute);

    /**
     * @param string $attribute
     *
     * @return mixed
     */
    abstract public function getAttributeValue($attribute);

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function validate()
    {
        $this->_errors = [];
        $preValidator = new PreValidator($this);
        foreach ($this->getValidators() as $attribute => $validators) {
            foreach ($validators as $validatorData) {
                $conditions = Arr::get($validatorData, Validator::PARAM_CONDITIONS);
                if ($conditions && !$preValidator->check($conditions)) {
                    continue;
                }
                $validatorName = $validatorData[0];
                /** @var $class \tradersoft\model\validator\Validator */
                $class = 'tradersoft\model\validator\\'.$validatorName;
                $class::validate($this, $attribute, $this->getAttributeValue($attribute), $validatorData[1]);
            }
        }

        return !$this->hasErrors();
    }

    /**
     * @param string|null $attributeName
     *
     * @return array
     * @throws \Exception
     */
    public function getValidators($attributeName = null)
    {
        if (empty($this->_activeValidators)) {
            $this->_initValidators();
        }

        if ($attributeName) {
            if (isset($this->_activeValidators[$attributeName])) {
                return $this->_activeValidators[$attributeName];
            }
            return [];
        } else {
            return $this->_activeValidators;
        }
    }

    /**
     * @inheritDoc
     */
    public function hasErrors($attribute = null)
    {
        return $attribute === null ? !empty($this->_errors) : isset($this->_errors[$attribute]);
    }

    /**
     * @inheritDoc
     */
    public function getErrors($attribute = null)
    {
        if ($attribute === null) {
            return $this->_errors === null ? [] : $this->_errors;
        }

        return isset($this->_errors[$attribute]) ? $this->_errors[$attribute] : [];
    }

    /**
     * @inheritDoc
     */
    public function getFirstError($attribute)
    {
        return isset($this->_errors[$attribute]) ? reset($this->_errors[$attribute]) : null;
    }

    /**
     * @inheritDoc
     */
    public function addError($attribute, $error = '')
    {
        $this->_errors[$attribute][] = $error;
    }

    /**
     * @throws Exception
     */
    protected function _initValidators()
    {
        $validators = [];
        foreach ($this->rules() as $rule) {
            if (!$this->_isValidRule($rule)) {
                continue;
            }
            $validator = $rule[1];
            $param = Arr::get($rule, 2);
            $conditions = Arr::get($rule, Validator::PARAM_CONDITIONS, []);
            if (is_array($rule[0])) {
                foreach ($rule[0] as $itemAttribute) {
                    $attribute = $itemAttribute;
                    $this->_checkAttribute($attribute);
                    $validators[$attribute][] = [$validator, $param, Validator::PARAM_CONDITIONS => $conditions];
                }
            } else {
                $attribute = $rule[0];
                $this->_checkAttribute($attribute);
                $validators[$attribute][] = [$validator, $param, Validator::PARAM_CONDITIONS => $conditions];
            }
        }

        $this->_activeValidators = $this->_filterValidators($validators);
    }

    /**
     * @param $attribute
     *
     * @throws Exception
     */
    protected function _checkAttribute($attribute)
    {
        if (!$this->hasAttribute($attribute)) {
            throw new \Exception("Unknown attribute. [attribute = $attribute]");
        }
    }

    /**
     * @param $rule
     *
     * @return bool
     * @throws Exception
     */
    protected function _isValidRule($rule)
    {
        if (empty($rule[0]) || empty($rule[1])) {
            throw new Exception(Translate::__('Invalid validation rule'));
        }
        $validator = 'tradersoft\model\validator\\' . $rule[1];
        try {
            return class_exists($validator);
        } catch (Exception $e) {
        }

        return false;
    }

    /**
     * @param array $validators
     *
     * @return array
     * @throws \Exception
     */
    protected function _filterValidators(array $validators)
    {
        $result = [];
        foreach ($validators as $attribute => $validation) {
            $maxLengthValidator = $minLengthValidator = null;
            $min = $max = null;
            foreach ($validation as $validatorData) {
                $class = 'tradersoft\model\validator\\' . $validatorData[0];
                switch ($class) {
                    case Max_Length::class :
                        // no break
                    case MaxLength::class :
                        /** @var MaxLength $class */
                        $length = $class::prepareLength($validatorData[1]);
                        if (is_null($min) || $min > $length) {
                            $min = $length;
                            $maxLengthValidator = $validatorData;
                        }
                        break;
                    case Min_Length::class :
                        // no break
                    case MinLength::class :
                        /** @var MinLength $class */
                        $length = $class::prepareLength($validatorData[1]);
                        if (is_null($max) || $max < $length) {
                            $max = $length;
                            $minLengthValidator = $validatorData;
                        }
                        break;
                    default:
                        $result[$attribute][] = $validatorData;
                        break;
                }
            }

            if ($maxLengthValidator) {
                $result[$attribute][] = $maxLengthValidator;
            }
            if ($minLengthValidator) {
                $result[$attribute][] = $minLengthValidator;
            }
        }

        return $result;
    }
}