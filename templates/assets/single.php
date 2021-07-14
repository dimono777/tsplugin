<?php

use tradersoft\helpers\Platform;

/** @var array $asset */
$asset = TSInit::$app->getVar('asset', []);

?>

<script>

  /**
   * competition date filter component
   */
  var financialAssetTypeComponent = (function($) {
    return {

      template: '#financial-asset-types-template',

      props: ['asset'],

      data: function() {
        return {
		  selectedInfo: 'cfd',
		  nodeData: {},
        };
	  },
	  
	  events: {
            'assets-node-update': function (msg) {
                this.nodeData = msg[this.asset.id];
            }
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
            <div class="tab-content active">
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
  if (singleAsset) {
	singleAsset["<?php echo $asset['name'] ?>"] = <?php echo json_encode($asset); ?>;
  } else {
	  var singleAsset = {};
	  singleAsset["<?php echo $asset['name'] ?>"] = <?php echo json_encode($asset); ?>;
  }

</script>

<div class="gradient-section asset-section financial-assets-component" data-assetid="<?php echo $asset['id'] ?>">
    <div class="section-inner">
        <div class="open-tabs">
            <div class="accordion-content asset-content" style="display: block">
                <div class="col-lg-6">
                    <financial-asset-types :asset="singleAsset['<?php echo $asset["name"] ?>']"></financial-asset-types>
                </div>
                <div class="col-lg-6">
                    <div class="trading-hours">
                        <iframe class="iframe"
                                src="<?php echo Platform::getURL(Platform::URL_MINI_CHART_FRAME); ?>?asset_id=<?php echo $asset['id'] ?>&period_id=M1'">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>