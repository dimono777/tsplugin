/**
 * @param data
 * @param assetCollection
 * @constructor
 */
function TSMarket(data, assetCollection) {
    this.id = 0;
    this.name = "";
    this.is_active = true;
    this.sort = 0;
    this.systemName = "";
    this.orderBy = "popularity";

    this.assets = assetCollection || new TSAssetCollection();

    this.setData(data || {});
}

TSMarket.prototype = {

    /**
     * @param assets
     */
    setAssets: function (assets) {
        if (assets instanceof TSAssetCollection) {
            this.assets = assets;
        } else {
            this.assets.setElements(new TSCollection(assets).map(function (asset) {
                return new TSAsset(asset);
            }));
        }
        return this;
    },

    getAssets: function () {
        return this.assets;
    },

    /**
     * Set asset data
     * @param data
     */
    setData: function (data) {
        this.id = parseInt(data.id || 0);
        this.name = (data.nameTranslation || data.name || "").toString();
        this.is_active = !!data.is_active || true;
        this.sort = parseInt(data.sort || 0);
        this.systemName = (data.name || "").toString();
        if (data.orderBy) {
            this.orderBy = data.orderBy.toString();
        }
        if (data.assets) {
            this.setAssets(data.assets);
        }
    }
};
