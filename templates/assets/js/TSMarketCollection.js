/**
 * Market collection. Market collection with asset collection.
 * @param markets
 * @constructor
 */
function TSMarketCollection(markets) {
    TSCollection.call(this, markets);
}

TSMarketCollection.prototype = Object.create(TSCollection.prototype, {
    setAssets: {
        value: function (assets) {
            this.forEach(function (market) {
                market.setAssets(assets);
            });
            return this;
        },
        configurable: true, writable: true, enumerable: true
    },

    addElement: {
        value: function (market) {
            if (market && market.prototype !== TSMarket.prototype) {
                market = new TSMarket(market);
            }
            return TSCollection.prototype.addElement.call(this, market);
        },
        configurable: true, writable: true, enumerable: true
    },

    getElements: {
        value: function () {
            return this
                .filter(function (market) {
                    return market.is_active;
                })
                .orderBy("sort", true)
                .elements;
        }, configurable: true, writable: true, enumerable: true
    }
});

TSMarketCollection.prototype.constructor = TSMarketCollection;