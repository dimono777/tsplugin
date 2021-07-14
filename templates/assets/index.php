<?php
/**
 * @var $markets array
 * @var $assetsIdsList array
 * @var $marketsData array
 * @var string $mainDomain
 */

use tradersoft\helpers\Platform;

$assetsIndexData = TSInit::$app->getVar('assetsIndexData',[]);
extract($assetsIndexData);

?>
<script>
  
  /**
   * competition date filter component
   */
  var financialAssetTypeComponent = (function($) {
    return {
      
      template: '#financial-asset-types-template',
      
      props: ['asset', 'nodeData'],
      
      ready: function() {
        tooltip.init($('a[data-tooltip]'));
      },
      
      data: function() {
        return {
          selectedInfo: 'cfd',
        };
      },
      
      computed: {
        nameByType: function() {
          var nameAttr;
          switch (this.selectedInfo) {
            case 'cfd':
              nameAttr = 'nameCFD';
              break;
            
            default:
              nameAttr = 'name';
              break;
          }
          
          return this.asset[nameAttr];
        },
        
        workDay: function() {
          if (this.asset.workTime !== undefined) {
            return this.asset.workTime.startDay + '-' + this.asset.workTime.endDay;
          }
          
          return '';
        },
        
        workTime: function() {
          if (this.asset.workTime !== undefined) {
            return this.asset.workTime.startTime + '-' + this.asset.workTime.endTime;
          }
          
          return '';
        },
      },
      methods: {
        select: function(infoType) {
          this.selectedInfo = infoType;
        },
      },
    };
  })(window.jQuery);

</script>


<script type="x-template" id="financial-asset-types-template">

    <h3 class="asset-title">{{ nameByType }}</h3>
    <div v-if="workDay || workTime" class="asset-hours">
        <span><?php echo \TS_Functions::__('Trading Hours'); ?></span>
        {{ workDay }},
        {{ workTime }} (GMT)
    </div>
    <div class="statistic">
        <div class="statistic-table">
            <ul class="nav nav-tabs" role="tablist">
                <li class="active">
                    <a href="#cfd" aria-controls="cfd" role="tab" data-toggle="tab" aria-expanded="false" @click="selectedInfo = 'cfd'">
                        <?php echo \TS_Functions::__('CFD'); ?>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="statistic-block row " id="cfd" v-show="selectedInfo == 'cfd'">
                    <div class="col-lg-6">
                        <table>
                            <tbody>
                            <tr v-for="cfd in asset.cfd">
                                <td>{{ cfd.name }}</td>
                                <td>{{ cfd.value }}
                                    <a data-tooltip="{{ cfd.description }}">
                                        <img src="<?php echo \tradersoft\helpers\Assets::findUrl('/img/ico-info.png'); ?>" class="img-responsive" alt="">
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-6 text-center">
                        <div class='payment payment-up'>
                            <div class="current">
                                <span>{{ nodeData.sell_rate }}</span>
                            </div>
                        </div>

                        <div class='payment payment-down'>
                            <div class="current">
                                <span>{{ nodeData.buy_rate }}</span>
                            </div>
                        </div>
                        <div class="trade-block">

                            <div :class="{
                                'rate': true,
                                'rate-up': nodeData.percent > 0,
                                'rate-down': nodeData.percent < 0
                            }">
                                {{ nodeData.percent }}%
                                <div class="arrow" v-show="nodeData.percent != 0"></div>
                            </div>

                        </div>
                        <div class="asset-button">
                            <a class="btn btn-green" href="{{ asset.buttonUrl }}">{{ asset.buttonNameCFD }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</script>

<script>

    /**
     * competition date filter component
     */
    var financialAssetComponent = {

        template: '#financial-asset-template',

        props: ['asset', 'selectedAsset'],

        data: function() {
            return {
                needShow: false,
                nodeData: {}
            }
        },

        events: {
            'assets-node-update': function (msg) {
                this.nodeData = msg[this.asset.id];
            }
        },

        methods: {
            selected: function() {
                this.selectedAsset = this.asset.id;
            },
            accordionToggle: function(needShow) {
                // Crutch so that the old functionality does not interfere
                if ('undefined' !== typeof $) {
                    $('.accordion-item-wrap').find('.accordion-toggle').off('click');
                }
                this.$dispatch('deactivate-accordion-tabs');
                this.needShow = needShow;
            },
            deactivateAccordionTab: function() {
                this.needShow = false;
            }
        }
    };
</script>

<script type="x-template" id="financial-asset-template">
    <div class="accordion-item" @click="selected()"  v-bind:class="{'active' : needShow}">
        <div class="accordion-toggle" @click.prevent="accordionToggle(!needShow)">
            <div class="title">
                {{ asset.name }}
            </div>
            <div style="display: none">
                <!--                for tests, can be delete-->
                {{ asset.popularity }}
            </div>
            <div class="open-arrow"></div>
        </div>

        <div class="accordion-content asset-content" v-bind:style="{display: needShow ? 'block' : 'none'}">
            <div class="col-lg-6">

                <financial-asset-types v-if="selectedAsset == this.asset.id"
                                       :node-data.sync="nodeData"
                                       :asset="asset">
                </financial-asset-types>
            </div>
            <div class="col-lg-6">
                <div class="trading-hours">
                    <iframe class="iframe"
                            v-if="selectedAsset == this.asset.id"
                            :src="'<?php echo Platform::getURL(Platform::URL_MINI_CHART_FRAME); ?>?asset_id=' + this.asset.id + '&period_id=M1'">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
    <div class="separator-h"></div>
</script>

<script>

    /**
     * financial market component
     */
    var financialMarketComponent = {

        template: '#financial-market-template',

        props: ['assets', 'market'],

        data: function() {
            return {
                selected: ''
            }
        },
        events: {
            'deactivate-accordion-tabs': function () {
                for (let index in this.$children) {
                    this.$children[index].deactivateAccordionTab()
                }
            }
        }
    };
</script>


<script type="x-template" id="financial-market-template">

    <div class="text">
        <p>{{{ market.desc }}}</p>
    </div>

    <div class="accordion-item-wrap">
        <financial-asset v-for="asset in assets"
                         :selected-asset.sync="selected"
                         :asset="asset">
        </financial-asset>
    </div>
</script>

<script>
    var markets = <?php echo json_encode($markets); ?>;
    var marketsData = <?php echo json_encode($marketsData, true); ?>;
    var assetsIdsList = <?php echo json_encode($assetsIdsList); ?>;
</script>

<div class="gradient-section asset-section" id="financial-assets">
    <div class="section-inner">
        <div class="open-tabs">
            <ul class="nav nav-tabs" role="tablist">

                <li v-for="market in markets"
                    :class="[
                    market.id == currentMarket ? 'active' : '',
                    market.systemName + '-item'
                    ]">
                    <a href="#{{ market.systemName }}"
                       aria-controls="{{ market.systemName }}"
                       role="tab"
                       data-toggle="tab"
                       @click="currentMarket = market.id"
                       :aria-expanded="market.id == currentMarket ? 'true' : ''"
                       onclick="return false;"
                    >
                        <span>{{ market.nameTranslation }}</span>
                    </a>
                </li>

            </ul>
            <div class="tab-content">
                <div v-for="market in markets" v-show="market.id == this.currentMarket">
                    <financial-market :assets="marketsData[market.name]" :market="market">
                    </financial-market>
                </div>
            </div>
        </div>
    </div>
</div>