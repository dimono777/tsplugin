<?php
namespace tradersoft\model;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Interlayer_Stat;
use tradersoft\helpers\TS_Setting;

/**
 * Class for connect to Stats service
 *
 * @package tradersoft\model
 */
class Stats
{

    /**
     * @deprecated
     */
    const OPTION_DOMAIN = 'stats_api_domain';

    /**
     * @deprecated
     */
    const OPTION_SECRET_KEY = 'stats_key';

    private $_showPixels = true;

    /**
     * ignore pixels for showing
     * function getPixelsContent will be ignored pixels
     *
     * @author Roman Lazarskiy <roman.lazarskiy@tstechpro.com>
     */
    public function ignorePixels()
    {
        $this->_showPixels = false;
    }

    /**
     * get pixels content form stats
     *
     * @author Roman Lazarskiy <roman.lazarskiy@tstechpro.com>
     *
     * @return string
     */
    public function getPixelsContent()
    {
        if (!$this->_showPixels || \TSInit::$app->trader->isGuest) {
            return '';
        }

        $hashId = \TSInit::$app->trader->accountNumber;

        if (!$hashId) {
            return '';
        }

        $response = Interlayer_Stat::getPixelsByTraderId($hashId);

        $pixelsList = Arr::get($response, 'pixels', []);
        return implode("\n", $pixelsList);
    }

    /**
     * send api request to stats server
     *
     * @author Roman Lazarskiy <roman.lazarskiy@tstechpro.com>
     *
     * @param $url
     * @param $params
     * @return bool|mixed
     * @deprecated use {@code Interlayer_Stat} instead
     */
    private function _sendRequest($url, array $params = [])
    {
        if (!$url) {
            return false;
        }

        $apiDomain = TS_Setting::get(self::OPTION_DOMAIN);
        $secretKey = TS_Setting::get(self::OPTION_SECRET_KEY);

        if (!$apiDomain || !$secretKey) {
            return false;
        }

        /** @var string $fullUrl full api url with clear slashes */
        $fullUrl = rtrim($apiDomain, '/')
            . '/'
            . ltrim($url, '/');

        $stringForCrc = $secretKey . urldecode(http_build_query($params));
        $params['CRC'] = strtoupper(md5($stringForCrc));

        $requestBody = http_build_query(['data' => json_encode($params)]);

        $ch = curl_init($fullUrl);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $data = curl_exec($ch);

        curl_close($ch);

        return $data;
    }

}