<?php
/**
 * @var string $marketsWithAssets
 */

use tradersoft\helpers\Platform;

$marketsWithAssets = TSInit::$app->getVar('marketsWithAssets');
$crmLibUrl = Platform::getURL(Platform::URL_SCRIPT_CRM_LIB);



/** JUST TO DECEIVE IDE  */
if (false) { ?>
<script>
<?php } ?>

<?php
// define global variable for using in templates/assets/js/TSIOWSConnection.js
echo "var ts_platform_crm_lib_url = '{$crmLibUrl}';";
?>

(function ($) {
    $(function (marketCollection) {
        window.marketCollection = marketCollection;
        var connection,
            publisher = new TSPublisher(),
            script = document.createElement("script"),
            head = document.getElementsByTagName("head")[0],
            popularitySortedAssetCollection = new TSSortedAssetCollection(
                new TSAssetCollection(),
                "popularity",
                false
            ),
            percentSortedAssetCollection = new TSSortedAssetCollection(
                new TSAssetCollection(),
                "percent",
                false,
                Math.abs
            );

        marketCollection.forEach(function (market) {
            popularitySortedAssetCollection.addAllElements(market.getAssets());
            percentSortedAssetCollection.addAllElements(market.getAssets());
        });

        marketCollection.addElement(
            new TSMarket(
                {
                    name: '<?php
                        echo TS_Functions::translateByKey(
                            '[Most popular:nameTranslation]',
                            '[assets-market]',
                            false
                        ) ? : "Most popular";

                        ?>',
                    is_active: true,
                    sort: 0,
                    id: -6554
                },
                popularitySortedAssetCollection
            )
        );

        marketCollection.addElement(
            new TSMarket(
                {
                    name: '<?php
                        echo TS_Functions::translateByKey(
                            '[Top movers:nameTranslation]',
                            '[assets-market]',
                            false
                        ) ? : "Top movers";

                        ?>',
                    is_active: true,
                    sort: 0,
                    id: -5418,
                    orderBy: "percent"
                },
                percentSortedAssetCollection
            )
        );

        publisher.subscribe(new TSAssetSubscription(popularitySortedAssetCollection));

        window.miniAssetPanel = new Vue({
                el: "#mini-asset-panel",

                data: {
                    markets: marketCollection.getElements(),
                    selectedMarket: null
                },

                methods: {
                    selectMarket: function (market) {
                        this.selectedMarket = market;
                    },
                    redirect: function (marketAsset) {
                        window.open(marketAsset.buttonUrl, "_blank");
                    }
                },

                computed: {
                    marketAssets: function () {
                        if (this.selectedMarket === null) {
                            return [];
                        }

                        var AssetCollection = new TSAssetCollection(this.selectedMarket.getAssets().getElements());

                        if (typeof this.selectedMarket.orderBy
                            !== "undefined"
                            && this.selectedMarket.orderBy
                            === "percent") {
                            AssetCollection = AssetCollection.orderByPercent();
                        }
                        else {
                            AssetCollection = AssetCollection.orderByPopularity();
                        }

                        return AssetCollection.limit(5).getElements();
                    }
                },

                created: function () {
                    this.selectMarket(this.markets[0]);
                }
            });

        connection = new TSIOWSConnection();
        var subscribe = function () {
            connection.addSubscriptionPublisher(
                publisher,
                'assetsRates',
                popularitySortedAssetCollection.map(function (asset) {
                    return asset.id;
                }).getElements()
            );
        };
        subscribe();

    }.bind($, new TSMarketCollection(<?php echo $marketsWithAssets; ?>)));
})(window.jQuery);