<?php
namespace tradersoft\helpers;

class Interlayer_Platform extends Interlayer {

	public static function getAssetsIndex($params = [])
	{
		$result = self::jsonDecode(
			self::sendRequest('/api/get-assets-index', $params),
			true
		);
        return ($result) ? : [];
	}

	public static function getAssetByName($name)
    {
        $result = self::jsonDecode(
            self::sendRequest('/api/get-assets', ['q_search' => $name]),
            true
        );

        if (!$result || !is_array($result)) {
            return [];
        }

        return reset($result);
    }

	public static function getAllMarkets($params = [])
	{
        $result = self::jsonDecode(
            self::sendRequest('/api/get-all-markets', $params),
            true
        );
        return ($result) ? : [];
	}

	public static function getAllAssetsCFD($params = [])
    {
        return self::jsonDecode(
            self::sendRequest('/api/get-all-assets-cfd', $params),
            true
        );
    }
}