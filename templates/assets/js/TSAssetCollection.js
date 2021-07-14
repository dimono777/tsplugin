/**
 * Asset collection. Collection with asset.
 * @param assets
 * @constructor
 */
function TSAssetCollection(assets) {
    TSCollection.call(this, assets);
}

TSAssetCollection.prototype = Object.create(TSCollection.prototype, {

    /**
     * Sort asset list by popularity
     */
    orderByPopularity: {
        value: function (asc) {
            return this.orderBy("popularity", typeof asc === "undefined" ? asc : 0)
        },
        configurable: true, writable: true, enumerable: true
    },

    /**
     * Sort asset list by percent
     */
    orderByPercent: {
        value: function (asc) {
            return this.orderBy("percent", typeof asc !== "undefined" ? asc : 0, Math.abs)
        },
        configurable: true, writable: true, enumerable: true
    },

    /**
     * Update asset
     */
    updateAsset: {
        value: function (asset) {
            var elem;
            if (!asset instanceof Object || typeof asset.id === "undefined") {
                return false;
            }
            elem = this.findBy("id", parseInt(asset.id));
            if (elem) {
                elem.update(asset);
                return true;
            }
            return false;
        },
        configurable: true, writable: true, enumerable: true
    },

    /**
     * Update assets list
     */
    updateAssets: {
        value: function (assets) {
            new TSCollection(assets).forEach(this.updateAsset.bind(this));
            return this;
        },
        configurable: true, writable: true, enumerable: true
    }
});

TSAssetCollection.prototype.constructor = TSAssetCollection;