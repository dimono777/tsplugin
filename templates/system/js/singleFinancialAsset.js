/**
 * competition date filter component
 */
Vue.component('financial-asset-types', financialAssetTypeComponent);

/**
 * base Vue instance
 */

var financialAssetsListEl = document.querySelectorAll('.financial-assets-component');

for (let el of financialAssetsListEl) {
	(function($) {
		return new Vue({
			el,
	
			data: {
				assetsIdsList: new Array(el.dataset.assetid),
				singleAsset: singleAsset
			  },
	  
			  ready: function() {
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
		});
	})(window.jQuery);
}
