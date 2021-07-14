<?php
/**
 * @var string $marketsWithAssets
 * @var string $allAssetUrl
 */

$allAssetUrl = TSInit::$app->getVar('allAssetUrl');
?>
<section id="mini-asset-panel">

    <!-- Tabs -->
    <div id="js-tradeTabs" class="tradeTabs">
        <button v-for="market in markets" class="tradeTabs__btn"
                v-bind:class="{active: market.id == selectedMarket.id}"
                @click="selectMarket(market)"
                :key="market.id">
            {{market.name}}
        </button>
    </div>

    <div class="tradeItem__block">

        <div id="tab_1" class="js-tradeItem tradeItem__wrapper">

            <!-- Item -->
            <div v-for="marketAsset in marketAssets" class="tradeItem">
                <div class="tradeItem__title">{{marketAsset.name}}</div>
                <div class="tradeItem__changeBlock">
                    <span class="tradeItem__text"><?php echo \TS_Functions::__('Change'); ?></span>
                    <span v-bind:class="{up: marketAsset.percent > 0, down: marketAsset.percent < 0}" class="tradeItem__percent">{{marketAsset.percent}}%</span>
                </div>
                <div class="tradeItem__sellBlock">
                    <span class="tradeItem__text" @click="redirect(marketAsset)"><?php echo \TS_Functions::__('Sell'); ?></span>
                    <span class="tradeItem__sum"><i class="fa fa-caret-down" aria-hidden="true"></i> {{marketAsset.sell_rate}}</span>
                </div>
                <div class="tradeItem__buyBlock">
                    <span class="tradeItem__text" @click="redirect(marketAsset)"><?php echo \TS_Functions::__('Buy'); ?></span>
                    <span class="tradeItem__sum"><i class="fa fa-caret-up" aria-hidden="true"></i> {{marketAsset.buy_rate}}</span>
                </div>
                <div class="tradeItem__btnWrap">
                    <a href="javascript:void(0);" @click="redirect(marketAsset)" class="tradeItem__btn_sell"><?php echo \TS_Functions::__('Sell'); ?></a>
                    <a href="javascript:void(0);" @click="redirect(marketAsset)" class="tradeItem__btn_buy"><?php echo \TS_Functions::__('Buy'); ?></a>
                </div>
                <a href="javascript:void(0);" @click="redirect(marketAsset)" class="tradeItem__btn up"><?php echo \TS_Functions::__('Trade'); ?></a>
            </div>

        </div>

    </div>

    <p class="tradableAssets__info"><?php echo \TS_Functions::__('Spreads may differ during volatile markets. The above prices are indicative only'); ?></p>
    <a href="<?php echo $allAssetUrl; ?>" class="tradableAssets__btn"><?php echo \TS_Functions::__('See All Assets'); ?></a>

</section>