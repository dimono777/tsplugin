/**
 * Asset model. Contains data about specific asset.
 * @param data
 * @constructor
 */
function TSAsset(data) {
    this.id = 0;
    this.name = "";
    this.nameCFD = "";
    this.marketId = 0;
    this.popularity = 0;
    this.buttonNameCFD = "";
    this.binaryOptions = {};
    this.cfd = [];
    this.workTime = {};
    this.desc = "";
    this.buy_rate = "";
    this.direction = 0;
    this.mid_rate = 0;
    this.percent = 0;
    this.sell_rate = "";
    this.system_name = "";
    this.buttonUrl = '';

    this.setData(data);
}

TSAsset.prototype = {
    setData: function (data) {
        this.id = parseInt(data.id) || 0;
        this.name = data.name || "";
        this.nameCFD = data.nameCFD || "";
        this.marketId = parseInt(data.marketId) || 0;
        this.popularity = parseFloat(data.popularity) || 0;
        this.buttonNameCFD = data.buttonNameCFD || "";
        this.binaryOptions = data.binaryOptions || {};
        this.cfd = data.cfd || [];
        this.workTime = data.workTime || {};
        this.desc = data.desc || "";
        this.buy_rate = parseFloat(data.buy_rate) || "";
        this.direction = parseInt(data.direction) || 0;
        this.mid_rate = parseFloat(data.mid_rate) || 0;
        this.percent = parseFloat(data.percent) || 0;
        this.sell_rate = parseFloat(data.sell_rate) || "";
        this.system_name = data.system_name || "";
        this.buttonUrl = data.buttonUrl || "";
    },

    update: function (data) {
        var key;
        for (key in data) {
            if (data.hasOwnProperty(key) && this.hasOwnProperty(key)) {
                this[key] = data[key];
            }
        }
    }
};
