function TSWLUriResolver() {
    this.hostname = window.location.hostname;
}

TSWLUriResolver.prototype = {
    isLocal: function () {
        return (this.hostname.indexOf('.local') !== -1);
    },

    getHostName: function () {
        return this.hostname.replace('www.', '');
    },

    getCrmLibURL: function () {
        var urlLocal = 'http://' + this.getHostName() + '/wp-content/plugins/tradersoft/templates/system/js/crmlib.js';
        var urlProd = ts_platform_crm_lib_url; // defined in templates/assets/mini.php:63
        return this.isLocal() ? urlLocal: urlProd;
    },

    loadScript: function (url, id, onLoad) {
        var script = document.createElement("script"),
            head = document.getElementsByTagName( "head" )[0];
        id = id || '';
        script.src = url;
        script.id = id;
        head.appendChild( script );
        if (typeof onLoad === "function") {
            script.onload = onLoad;
        }
    }
};


/**
 * Connect to socket
 * @constructor
 */
function TSIOWSConnection() {
    this.uriResolver = new TSWLUriResolver();
    this._crmLibID = 'crm-lib';
    if (!this.isLoadLib()) {
        this.loadLib();
    }
}

TSIOWSConnection.prototype = {
    isLoadLib: function () {
        return (typeof subscribe === "function");
    },

    /**
     * Load platform lib for socket connect
     */
    loadLib: function () {
        if (!document.getElementById(this._crmLibID)) {
            this.uriResolver.loadScript(
                this.uriResolver.getCrmLibURL(),
                this._crmLibID
            );
        }
    },

    /**
     * Subscribe to the node event
     */
    addSubscriptionPublisher: function (publisher, method, data) {
        try {
            if (this.isLoadLib()) {
                subscribe(method, data, function(response){
                    publisher.notify(response, method);
                });
            } else {
                var that = this;
                setTimeout(function(){
                    that.addSubscriptionPublisher(publisher, method, data)
                }, 1000);
            }
        } catch (e) {
            console.log(e);
        }
    }
};
