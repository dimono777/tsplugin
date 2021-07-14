<?php
namespace tradersoft\controllers;

use tradersoft\model\FinancialAsset;

/**
 * Assets index controller
 */
class Assets_Controller extends Base_Controller
{
    public function rules()
    {
        return [];
    }

    public function actionIndex()
    {
        $financialAsset = $this->_initFinancialAsset();

        $this->_setVar('assetsIndexData', [
            'markets' => $financialAsset->getMarkets(),
            'assetsIdsList' => $financialAsset->getAssetIds(),
            'marketsData' => $financialAsset->getAssets(),
            'mainDomain' => \TSInit::$app->request->getMainDomain()
        ]);
    }

    public function actionMini()
    {
        $financialAsset = $this->_initFinancialAsset();

        $this->_setVar(
            'marketsWithAssets',
            json_encode($financialAsset->getMarketsWithAssetsCFD())
        );
        $this->_setVar(
            'allAssetUrl',
            $financialAsset->getAllAssetUrl()
        );
    }

    public function actionSingle()
    {
        if (empty($this->params['assetName'])) {
            wp_die('Parameter <i>assetName</i> required for short code <b>[TS-ASSETS-SINGLE]</b>');
        }

        $this->_setVar(
            'asset',
            $this->_initFinancialAsset()->getAssetByName(strtolower($this->params['assetName']))
        );
    }

    /**
     * Return Financial Asset instance
     *
     * @return FinancialAsset
     *
     * @author Alexander Shpak <alexander.shpak@tstechpro.com>
     */
    protected function _initFinancialAsset() {
        $enabledMarkets = [];
        if (!empty($this->params['enableMarkets'])) {
            $enabledMarkets = array_map(
                'trim',
                explode(',', $this->params['enableMarkets'])
            );
        }

        return new FinancialAsset($enabledMarkets);
    }

}