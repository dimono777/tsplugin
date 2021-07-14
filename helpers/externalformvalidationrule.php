<?php

namespace tradersoft\helpers;

use Exception;

/**
 * Class ExternalFormValidationRule
 */
class ExternalFormValidationRule
{
    const FIELD_FULL_NAME = 'fullName';
    const FIELD_FIRST_NAME = 'firstName';
    const FIELD_LAST_NAME = 'lastName';
    const FIELD_MIDDLE_NAME = 'middleName';
    const FIELD_USERNAME = 'username';
    const FIELD_EMAIL = 'email';
    const FIELD_PHONE = 'phone';
    const FIELD_BUILDING_NUMBER = 'buildingNumber';
    const FIELD_COUNTRY = 'country';
    const FIELD_CURRENCY = 'currency';
    const FIELD_STATE = 'state';
    const FIELD_POSTAL_CODE = 'postalCode';
    const FIELD_TOWN = 'town';
    const FIELD_STREET = 'street';
    const FIELD_ADDRESS = 'address';
    const FIELD_LANGUAGE = 'language';
    const FIELD_PASSWORD = 'password';
    const FIELD_DOMAIN = 'domain';

    const VALIDATOR_EMAIL = 'email';
    const VALIDATOR_PASSWORD = 'password';
    const VALIDATOR_PERSONAL_DATA_FORMAT = 'personalDataFormat';
    const VALIDATOR_PATTERN = 'pattern';
    const VALIDATOR_MIN_LENGTH = 'minLength';
    const VALIDATOR_MAX_LENGTH = 'maxLength';
    const VALIDATOR_EXACT_LENGTH = 'exactLength';
    const VALIDATOR_NO_HTML = 'no_html';

    /**
     * @var array
     */
    protected static $_validationRules = [];

    /**
     * @var array
     */
    protected static $_rulesAlias = [
        self::VALIDATOR_EMAIL => 'emailFormat',
        self::VALIDATOR_PASSWORD => 'passwordFormat',
        self::VALIDATOR_PATTERN => 'regex',
        self::VALIDATOR_MIN_LENGTH => 'min_length',
        self::VALIDATOR_MAX_LENGTH => 'max_length',
        self::VALIDATOR_EXACT_LENGTH => 'exact_length',
    ];

    /**
     * @var array
     */
    protected static $_availableRules = [
        self::VALIDATOR_EMAIL,
        self::VALIDATOR_PASSWORD,
        self::VALIDATOR_PERSONAL_DATA_FORMAT,
        self::VALIDATOR_PATTERN,
        self::VALIDATOR_MIN_LENGTH,
        self::VALIDATOR_MAX_LENGTH,
        self::VALIDATOR_EXACT_LENGTH,
        self::VALIDATOR_NO_HTML,
    ];

    /**
     * @var array
     */
    protected static $_rulesWithoutRequiredParams = [
        self::VALIDATOR_NO_HTML,
    ];

    /**
     * @var bool
     */
    protected static $_rulesLoaded = false;

    /**
     * @return int
     */
    public static function getFullNameMaxSize()
    {
        return (int) static::getFieldRuleParams(
            static::FIELD_FULL_NAME,
            static::VALIDATOR_MAX_LENGTH,
            201
        );
    }

    /**
     * Create field rule
     *
     * @param       $fieldName
     * @param       $validationRuleName
     * @param null  $params
     * @param array $additionalParams example: ['msg' => 'text', ...]
     *
     * @return array
     * @throws Exception
     */
    public static function createFieldRule($fieldName, $validationRuleName, $params = null, array $additionalParams = [])
    {
        // check available rules
        if (!in_array($validationRuleName, static::$_availableRules)) {
            return [];
        }

        // check required params
        if (is_null($params) && !in_array($validationRuleName, static::$_rulesWithoutRequiredParams)) {
            return [];
        }

        $ruleName = Arr::get(static::$_rulesAlias, $validationRuleName, $validationRuleName);
        switch ($validationRuleName) {
            case self::VALIDATOR_EMAIL:
            case self::VALIDATOR_PASSWORD:
            case self::VALIDATOR_PATTERN:
                $params = is_array($params) ? $params : ['pattern' => $params];
                break;

            case self::VALIDATOR_MIN_LENGTH:
            case self::VALIDATOR_MAX_LENGTH:
            case self::VALIDATOR_EXACT_LENGTH:
                $params = is_array($params) ? $params : ['length' => $params];
                break;

            case self::VALIDATOR_PERSONAL_DATA_FORMAT:
                if (!array_key_exists('patternsList', $params)) {
                    return [];
                }
                break;

            case self::VALIDATOR_NO_HTML:
                $params = is_array($params) ? $params : [];
                break;

            default:
                return [];
        }

        if ($fullParams = array_merge($params, $additionalParams)) {
            return [$fieldName, $ruleName, $fullParams];
        }

        return [$fieldName, $ruleName];
    }

    /**
     * Getting rules for fields
     *
     * @param       $fields
     * @param array $additionalParamsForFields example: [configFieldName => [validationRuleName => ['msg' => 'text', ...]]
     *
     * @return array
     * @throws Exception
     */
    public static function getFieldsRules($fields, array $additionalParamsForFields = [])
    {
        $rules = [];
        foreach ($fields as $formFieldName => $configFieldName) {
            $additionalParamsForField = Arr::get($additionalParamsForFields, $configFieldName, []);
            if ($fieldRules = static::getFieldRules($formFieldName, $configFieldName, $additionalParamsForField)) {
                $rules = array_merge($rules, $fieldRules);
            }
        }

        return $rules;
    }

    /**
     * Getting rules for field
     *
     * @param       $formFieldName
     * @param       $configFieldName
     * @param array $additionalParamsForValidator example: [validationRuleName => ['msg' => 'text', ...]
     *
     * @return array
     * @throws Exception
     */
    public static function getFieldRules($formFieldName, $configFieldName, array $additionalParamsForValidator = [])
    {
        static::_loadRules();

        if (
            !array_key_exists($configFieldName, static::$_validationRules)
            || !is_array(static::$_validationRules[$configFieldName])
        ) {
            return [];
        }

        $rules = [];
        foreach (static::$_validationRules[$configFieldName] as $validator => $params) {
            $additionalParams = Arr::get($additionalParamsForValidator, $validator, []);
            if ($rule = static::createFieldRule($formFieldName, $validator, $params, $additionalParams)) {
                $rules[] = $rule;
            }
        }

        return $rules;
    }

    /**
     * Getting field rule params
     *
     * @param      $configFieldName
     * @param      $validationRuleName
     * @param null $defaultValue
     *
     * @return mixed|null
     */
    public static function getFieldRuleParams($configFieldName, $validationRuleName, $defaultValue = null)
    {
        static::_loadRules();

        return isset(static::$_validationRules[$configFieldName][$validationRuleName])
            ? static::$_validationRules[$configFieldName][$validationRuleName]
            : $defaultValue;
    }

    /**
     * @return array
     */
    public static function getAll()
    {
        static::_loadRules();

        return static::$_validationRules;
    }

    /**
     * Getting form validation rules from CRM
     *
     * @param false $refresh
     */
    protected static function _loadRules($refresh = false)
    {
        if (!static::$_rulesLoaded || $refresh) {
            static::$_validationRules = Interlayer_Crm::getFormValidationRules();
        }
        static::$_rulesLoaded = true;
    }
}