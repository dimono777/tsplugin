<?php

namespace tradersoft\model;

use tradersoft\helpers\Arr;
use tradersoft\helpers\Interlayer_Platform;
use tradersoft\helpers\Link;
use tradersoft\helpers\Platform;
use TS_Functions;

class FinancialAsset
{
    const STOCKS_MARKET_ID = 1;
    const FOREX_MARKET_ID = 2;
    const CRYPTO_MARKET_ID = 201;
    const COMMODITIES_MARKET_ID = 3;
    const INDICES_MARKET_ID = 4;

    const FOREX_ASSETS_CRYPTO_GROUP_ID = 4;

    /**
     * @var array Available markets on Platform, id => name pairs
     * (names will be appended in the code below)
     */
    protected $_availableMarkets = [
        self::FOREX_MARKET_ID => '',
        self::CRYPTO_MARKET_ID => '',
        self::COMMODITIES_MARKET_ID => '',
        self::INDICES_MARKET_ID => '',
        self::STOCKS_MARKET_ID => '',
    ];

    /**
     * @var array Enabled markets ids
     */
    protected $_enabledMarketsIds = [];

    /**
     * @var array Assets groups data grouped by market id
     * (contains asset group id => new market id to move asset into)
     */
    protected $_assetsGroupsReplacement = [
        self::FOREX_MARKET_ID => [
            self::FOREX_ASSETS_CRYPTO_GROUP_ID => self::CRYPTO_MARKET_ID
        ]
    ];

    /**
     * @var array Custom markets data grouped by market id
     */
    protected $_customMarkets = [
        self::CRYPTO_MARKET_ID => [
            'id' => self::CRYPTO_MARKET_ID,
            'name' => 'Cryptocurrencies',
            'is_active' => 1,
            'systemName' => 'cryptocurrencies'
        ]
    ];

    /**
     * @var array Assets mini config grouped by market id
     */
    protected $_assetsMiniConfig = [
        self::FOREX_MARKET_ID => [
            'groupIdKey' => 'forex_group_id'
        ]
    ];

    private $_assetsIndex;
    private $_assetsByName = [];
    private $_assetsCFD;
    private $_allMarkets;
    private $_assetIds;
    private $_notActiveAssets = [];

    /**
     * FinancialAsset constructor
     *
     * @param array $marketsIds
     */
    public function __construct(array $marketsIds = [])
    {
        $this->_enabledMarketsIds = (!empty($marketsIds))
            ? $marketsIds
            : array_keys(
                array_diff_key(
                    $this->_availableMarkets,
                    $this->_customMarkets
                )
            );
    }

    /**
     * @return array|null
     */
    public function getAssets()
    {
        if (!$this->_assetsIndex) {
            $this->_assetsIndex = Interlayer_Platform::getAssetsIndex();
            if (!$this->_assetsIndex) {
                return [0];
            }

            // Move assets between markets (if it is enabled) based on asset's group id
            foreach ($this->_assetsGroupsReplacement as $oldMarketId => $assetGroupData) {
                foreach ($assetGroupData as $assetGroupId => $newMarketId) {
                    if (in_array($newMarketId, $this->_enabledMarketsIds)) {
                        $assets = [];
                        if (isset($this->_assetsIndex[$this->_availableMarkets[$oldMarketId]])) {
                            foreach ($this->_assetsIndex[$this->_availableMarkets[$oldMarketId]] as $asset) {
                                if ($asset['groupId'] == $assetGroupId) {
                                    $assets[$asset['id']] = $asset;
                                    unset($this->_assetsIndex[$this->_availableMarkets[$oldMarketId]][$asset['id']]);
                                }
                            }
                        }
                        $this->_assetsIndex[$this->_availableMarkets[$newMarketId]] = $assets;
                    }
                }
            }

            $assets = [];
            $enabledMarketsNames = array_intersect_key(
                $this->_availableMarkets,
                array_flip($this->_enabledMarketsIds)
            );
            foreach ($this->_assetsIndex as $marketName => $assetsList) {
                // process only enabled markets
                if (in_array($marketName, $enabledMarketsNames)) {
                    $assets[$marketName] = $assetsList;
                    foreach ($assetsList as $assetKey => $asset) {
                        $assets[$marketName][$assetKey]['desc'] = TS_Functions::translateByKey(
                            "[{$asset['name']}:desc]", '[assets-index]'
                        );
                        $assets[$marketName][$assetKey]['buttonUrl'] = $this->_getAssetButtonUrl($asset['name'], $assetKey);

                        if (isset($asset['cfd'])) {
                            foreach ($asset['cfd'] as $cfdField => $cfd) {
                                $assets[$marketName][$assetKey]['cfd'][$cfdField]['description'] = TS_Functions::translateByKey(
                                    $cfd['name'] . ':description',
                                    '[assets-index-fields]'
                                );
                            }
                        }
                    }
                }
            }
            $this->_assetsIndex = $assets;
        }
        return $this->_assetsIndex;
    }

    /**
     * @return array
     */
    public function getAssetByName($name)
    {
        if (!empty($this->_assetsByName[$name])) {
            return $this->_assetsByName[$name];
        }

        $asset = Interlayer_Platform::getAssetByName($name);

        $asset['desc'] = TS_Functions::translateByKey(
            "[{$asset['name']}:desc]", '[assets-index]'
        );
        $asset['buttonUrl'] = $this->_getAssetButtonUrl($asset['name'], $asset['id']);

        if (isset($asset['cfd'])) {
            foreach ($asset['cfd'] as $cfdField => $cfd) {
                $asset['cfd'][$cfdField]['description'] = TS_Functions::translateByKey(
                    $cfd['name'] . ':description',
                    '[assets-index-fields]'
                );
            }
        }

        return $this->_assetsByName[$name] = $asset;
    }

    /**
     * @return array
     */
    public function getMarkets()
    {
        if (!$this->_allMarkets) {
            $marketsList = Interlayer_Platform::getAllMarkets();
            // add custom market if it is enabled
            foreach ($this->_customMarkets as $customMarketId => $customMarketData) {
                if (in_array($customMarketId, $this->_enabledMarketsIds)) {
                    $marketsList[] = $customMarketData;
                }
            }

            $markets = [];
            foreach ($marketsList as $market) {
                // append markets names to available markets array
                $this->_availableMarkets[$market['id']] = $market['name'];
                // process only enabled markets
                $marketIndex = array_search($market['id'], $this->_enabledMarketsIds);
                if ($marketIndex !== false) {
                    $market['desc'] = TS_Functions::translateByKey(
                        '[' . $market['name'] . ':desc]',
                        '[assets-market]',
                        false
                    );
                    $nameTranslation = TS_Functions::translateByKey(
                        '[' . $market['name'] . ':nameTranslation]',
                        '[assets-market]',
                        false
                    );
                    $market['nameTranslation'] = $nameTranslation ?: $market['name'];
                    // set markets order as in enabledMarketsIds
                    $market['sort'] = $marketIndex + 1;

                    $markets[$market['id']] = $market;
                }
            }
            $this->_allMarkets = $markets;
        }
        return $this->_allMarkets;
    }

    /**
     * @return array|null
     */
    public function getAssetIds()
    {
        if (!$this->_assetIds) {
            $this->_assetIds = call_user_func_array(
                'array_merge', array_map(
                    function ($assets) {
                        return array_column($assets, 'id');
                    },
                    $this->getAssets()
                )
            );
        }
        return $this->_assetIds;
    }

    /**
     * Get markets list with asset
     * @return array
     */
    public function getMarketsWithAssets()
    {
        $markets = $this->getMarkets();
        $assets = $this->getAssets();
        foreach ($markets as &$market) {
            if (isset($market['name']) && isset($assets[$market['name']])) {
                $market['assets'] = $assets[$market['name']];
            }
        }
        return $markets;
    }

    /**
     * Get list asset CFD
     * @return mixed
     */
    public function getAssetsCFD()
    {
        if (!$this->_assetsCFD) {
            $this->_assetsCFD = Interlayer_Platform::getAllAssetsCFD();
            if (!$this->_assetsCFD) {
                return [];
            }

            foreach ($this->_assetsCFD as $key => &$asset) {
                if (!is_array($asset)) {
                    unset($this->_assetsCFD[$key]);
                    continue;
                }
                if ($asset['is_active'] != 1) {
                    $this->_notActiveAssets[$key] = $this->_assetsCFD[$key];
                    unset($this->_assetsCFD[$key]);
                    continue;
                }
                $asset['desc'] = TS_Functions::translateByKey(
                    "[{$asset['name']}:desc]", '[assets-index]'
                );
                $asset['buttonUrl'] = $this->_getAssetButtonUrl($asset['name'], $asset['id']);
                $asset['percent'] = Arr::get($asset, 'percent_change', 0.00);
            }
            if (count($this->_assetsCFD) < 5) {
                $this->_addNotActiveAssets();
            }
        }
        return $this->_assetsCFD;
    }

    /**
     * Get markets list with asset CFD
     * @return array
     */
    public function getMarketsWithAssetsCFD()
    {
        $markets = $this->getMarkets();
        $assets = $this->getAssetsCFD();

        foreach ($assets as $asset) {
            // Move assets between markets (if it is enabled) based on asset's group id
            foreach ($this->_assetsGroupsReplacement as $oldMarketId => $assetGroupData) {
                foreach ($assetGroupData as $assetGroupId => $newMarketId) {
                    if (
                        in_array($newMarketId, $this->_enabledMarketsIds)
                        && isset($this->_assetsMiniConfig[$oldMarketId]['groupIdKey'])
                        && isset($asset[$this->_assetsMiniConfig[$oldMarketId]['groupIdKey']])
                        && $asset[$this->_assetsMiniConfig[$oldMarketId]['groupIdKey']] == $assetGroupId
                    ) {
                        $asset['market_id'] = $newMarketId;
                    }
                }
            }

            if (isset($markets[$asset['market_id']])) {
                $markets[$asset['market_id']]['assets'][$asset['id']] = $asset;
            }
        }

        return $markets;
    }

    /**
     * Get link for all asset
     * @return string
     */
    public function getAllAssetUrl()
    {
        if (\TSInit::$app->trader->isGuest) {
            return Link::getTraderRegistrationLink();
        }
        return Platform::getURL(Platform::URL_CFD_ID);
    }

    /**
     * Get link for each asset
     *
     * @param $assetName
     * @param $assetKey
     * @return string
     */
    protected function _getAssetButtonUrl($assetName, $assetKey)
    {
        if (\TSInit::$app->trader->isGuest) {
            // sign up link
            return Link::getTraderRegistrationLink();
        } elseif ((float)\TSInit::$app->trader->balance <= 0) {
            return Platform::getURL(Platform::URL_DEPOSIT_ID);
        }

        $buttonUrl = TS_Functions::translateByKey(
            '[' . $assetName . ':buttonUrl]',
            '[assets-index]',
            false
        );


        if (empty($buttonUrl)) {
            // refer to Trading Room
            $buttonUrl = rtrim(Platform::getURL(Platform::URL_CFD_ID), '/') . '/' . $assetKey;
        }

        return $buttonUrl;
    }

    protected function _addNotActiveAssets()
    {
        while (count($this->_assetsCFD) < 5 && count($this->_notActiveAssets) > 0) {
            array_push($this->_assetsCFD, array_shift($this->_notActiveAssets));
        }
    }
}