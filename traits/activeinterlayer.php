<?php
namespace tradersoft\traits;

use tradersoft\helpers\Config;
use TSInit;
use tradersoft\helpers\Arr;
use tradersoft\helpers\Interlayer;
use tradersoft\helpers\Interlayer_Crm;
use tradersoft\model\form\components\Result;

/**
 * Trait ActiveInterlayer
 * @package tradersoft\traits
 *
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */
trait ActiveInterlayer
{
    protected $_unavailableParams = [
        'template',
        'ajaxEnable',
    ];
    protected $_apiVersion = 2;

    /**
     * @param $validationErrors
     * @return array
     */
    abstract protected function _prepareValidationErrors($validationErrors);

    /**
     * @return array
     */
    abstract protected function _getShortCodeParams();

    /**
     * @return string
     */
    protected function _getApiVersion()
    {
        return $this->_apiVersion;
    }

    /**
     * @param string $apiMethod
     * @param array $params
     * @param array $additionalParams
     * @return Result
     */
    protected function _send($apiMethod, array $params, $additionalParams = [])
    {
        $requestData = $this->_getBuilderFormParams() + [
            'data' => $params,
            'additionalData' => $this->_getRequiredRequestParams() + $additionalParams,
        ];

        $resultData = json_decode(Interlayer::sendRequest("/api/$apiMethod/", $requestData), true);

        $returnCode = Arr::get($resultData,'returnCode', Interlayer_Crm::RESPONSE_CODE_UNSPECIFIED_ERROR);
        $description = $this->__prepareCRMDescription($returnCode, Arr::get($resultData,'description', 'System error'));
        $validationErrors = Arr::get($resultData,'formValidationErrors');
        unset($resultData['returnCode'], $resultData['description'], $resultData['formValidationErrors']);

        return $this->_prepareResult(
            $returnCode,
            $description,
            $resultData ? : [],
            $this->_prepareValidationErrors($validationErrors)
        );
    }

    /**
     * @param $code int
     * @param $message string
     * @param $data array
     * @param $errors array
     * @return Result
     */
    protected function _prepareResult($code, $message = '', array $data = [], array $errors = null)
    {
        return Result::getResult($code, $message, $data, $errors);
    }

    protected function _getBuilderFormParams()
    {
        $shortCodeParams = (array)$this->_getShortCodeParams();
        $type = $shortCodeParams['type'];
        unset($shortCodeParams['type']);

        $params = [
            'version' => $this->_getApiVersion(),
            'ip' => TSInit::$app->request->userIP,
            'domain' => TSInit::$app->request->hostName,
            'lang' => Interlayer_Crm::getCurrentLanguage(),
            'type'=> $type,
            'additionalParams' => array_diff_key($shortCodeParams, array_flip($this->_unavailableParams)),
        ];

        if (!TSInit::$app->trader->isGuest) {
            $params['leadId'] = TSInit::$app->trader->get('crmId');
        }

        return $params;
    }

    protected function _getRequiredRequestParams()
    {
        //fix for local
        $request = TSInit::$app->request;
        $url = ($request->isLocal) ? 'kievphp.com' : $request->getHostName();
        $url .=  $request->getPath();

        return [
            'client_time' => date('Y-m-d H:i:s'),
            'client_timezone_offset' => 0,
            'language' => Interlayer_Crm::getCurrentLanguage(),
            'url' => $url,
            'ip' => $request->userIP,
        ];
    }

    protected function __prepareCRMDescription($returnCode, $description)
    {
        $errors = Config::get('crm_errors_messages');
        if(isset($errors[$returnCode])) {
            return \TS_Functions::__($errors[$returnCode]);
        }

        return \TS_Functions::__($description);
    }
}