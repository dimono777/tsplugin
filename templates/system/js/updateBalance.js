jQuery(document).ready(function () {
    var traderId = GLOBAL.crmHashId;
    var currencyPrecision = GLOBAL.currencyPrecision;
    var currentFinanceInfoTypeId = GLOBAL.currentFinanceInfoTypeId;

    if (traderId) {
        initSocketConnect();
    }

    function initSocketConnect() {
        try {
            var responseField;

            if (currentFinanceInfoTypeId == 0) {
                responseField = 'balance';
            } else {
                responseField = 'equity';
            }

            subscribe('userInfo', {'accountID':traderId}, function(response){
                if (typeof response[responseField] != undefined && !isNaN(parseFloat(response[responseField]))) {
                    jQuery('#header-user-balance').text(balanceFormat(response[responseField]));
                }
            });
        } catch (e) {
            console.log(e);
        }
    }

    /**
     * Comma for thousand and dot for cents
     * @autor Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @param string b
     * @returns string
     */
    function balanceFormat(b) {
        return parseFloat(b).toFixed(currencyPrecision).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
    }
});
