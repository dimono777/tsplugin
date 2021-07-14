function TSAssetSubscription(assetCollection) {
    this._assetCollection = assetCollection;
}

TSAssetSubscription.prototype = {
    update: function (data) {
        this._assetCollection.updateAssets(data);
    }
};
