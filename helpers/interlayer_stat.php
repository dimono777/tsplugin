<?php
namespace tradersoft\helpers;

/**
 * Interlayer helper for stat project.
 * @author Dmitriy Kachurovskiy <dmitriy.kachurovskiy@tstechpro.com>
 */
class Interlayer_Stat extends Interlayer
{
    const RESPONSE_CODE_SUCCESS = 0;
    const RESPONSE_CODE_WRONG_DOMAIN = 13;
    const RESPONSE_CODE_WRONG_CRC = 90;
    const RESPONSE_CODE_UNSPECIFIED_ERROR = 100;

    /**
     * @param $traderHashId
     * @param $protocol
     *
     * @return mixed
     */
    public static function getPixelsByTraderId($traderHashId, $protocol = null)
    {
        return self::jsonDecode(
            self::sendRequest(
                '/api/get-pixels-by-trader-id/',
                [
                    'traderHashId' => $traderHashId,
                    'protocol' => $protocol ?: \TS_Functions::getProtocol(),
                ]
            )
        );
    }
}