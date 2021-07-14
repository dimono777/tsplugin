/**
 * Created by Roman Lazarsky on 13.03.2016.
 */


/**
 * competition date filter component
 */
Vue.component('financial-market', financialMarketComponent);
Vue.component('financial-asset', financialAssetComponent);
Vue.component('financial-asset-types', financialAssetTypeComponent);


/**
 * base Vue instance
 */
var financialAssets = (function($) {
  return new Vue(
      {
        
        el: '#financial-assets',
        
        data: {
          markets: markets,
          currentMarket: null,
          marketsData: marketsData,
          assetsIdsList: assetsIdsList,
        },
        
        /**
         * sort markets
         */
        ready: function() {
          
          /** sort markets */
          this.markets = this.$options.filters.orderBy(
              this.markets,
              'sort',
              1,
          );
          
          /** sort marketsData */
          for (var marketName in this.marketsData) {
            this.marketsData[marketName] =
                this.$options.filters.orderBy(
                    this.marketsData[marketName],
                    'popularity',
                    -1,
                );
          }
          
          /** update current market after sort */
          this.setCurrentMarketByHash();
          
          $(window).on('hashchange', function() {
            financialAssets.setCurrentMarketByHash();
          });
          
          var that = this;
          /** create node connections for get assets rates */
          document.addEventListener(
              'DOMContentLoaded', function() {
                WLConnections(
                    'assetsRates',
                    that.assetsIdsList,
                    that.updateNodeData
                );
              });
        },
        
        methods: {
          
          /**
           * get first market or market from url hash
           * @returns {*}
           */
          setCurrentMarketByHash: function() {
            var hashLink = window.location.hash,
                selectedMarket = null;
            
            if (hashLink) {
              selectedMarket =
                  this.markets.find(function(market) {
                    return '#' + market.systemName
                           == hashLink;
                  });
            }
            
            if (!selectedMarket) {
              /** get first by default */
              var firstKey = this.getFirstKey(
                  this.markets);
              
              selectedMarket =
                  this.markets[firstKey].$value;
            }
            
            this.currentMarket = selectedMarket.id;
          },
          
          updateNodeData: function(c) {
            //send event for all children
            this.$broadcast('assets-node-update', c);
          },
          
          /**
           * return first key of object or array
           * Object.keys(data)[0] doesn't suitable because it returns
           * smallest key (not the first)
           */
          getFirstKey: function(data) {
            for (var i in data) {
              return i;
            }
          }
        }
      }
  );
})(window.jQuery);