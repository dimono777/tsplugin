Vue.filter('numberStyle', function(val) {
    var options = { minimumFractionDigits: this.roundLvl, maximumFractionDigits: this.roundLvl};
    return Number(val).toLocaleString("en-US", options)
});

Vue.filter('zeroOnLowBalance', function(val) {
  return (this.minAmount > this.balance) ? 0 : val;
});

Vue.filter('currencyDisplay', {
    // model -> view
    // formats the value when updating the input element.
    read: function(val, currencySymbol) {
        if (val[0] !== currencySymbol && !this.amountError) {
            return currencySymbol + val.toString();
        }

        return val;
    },

    // view -> model
    // formats the value when writing to the data.
    write: function(val) {
        /**
         * delete first symbol if currency
         */
        if (val.charAt(0) == currencySymbol) {
            val = val.substring(1);
        }

        /**
         * delete all coma
         */
        return val.replace(/,/g, '');
    }
});

Vue.filter('truncate', {

    read: function(val, roundTo) {
        val = parseFloat(val);
        if (typeof roundTo !== 'undefined') {
            val = val.toFixed(roundTo);
        }
        return val;
    },

    write: function(val, roundTo) {
        val = parseFloat(val.replace(',', ''));
        if (typeof roundTo !== 'undefined') {
            val = val.toFixed(roundTo);
        }
        return parseFloat(val);
    }
});

//Vue.component('withdrawal-list', withdrawalList);

new Vue({

    el: '#withdraw',

    data: {
        messages: messages,
        token: traderToken,
        balance: balance,
        balanceWithBonuses: balance,
        roundLvl: roundLvl,
        minAmount: minAmount,
        amount: minAmount,
        maintenanceFee: maintenanceFee,
        feeProcessing: feeProcessing,
        disabled: false,
        currencySymbol: currencySymbol,
        submitWithdrawalForm: submitWithdrawalForm,

        showLoader: true
    },

    ready: function () {
        this.checkBalance();

        try {
            /** create node connections for get trader balance updates */
            SiteWithdrawal('withdrawal', this.token, this.updateBalanceViaNode);

        } catch (e) {
        }

        if (this.showLoader) {
            this.showLoader = false;
        }
    },

    computed: {

        withdrawalFeeEnabled: function () {
            return this.feeProcessing.hasOwnProperty('feeEnabled')
                && this.feeProcessing.feeEnabled;
        },

        canCalculateWithdrawalFee: function () {
            return this.feeProcessing.hasOwnProperty('settings')
                && !(Array.isArray(this.feeProcessing.settings) && this.feeProcessing.settings.length === 0)
        },

        currentWithdrawalFee: function () {
            if (!this.withdrawalFeeEnabled || !this.canCalculateWithdrawalFee) {
                return 0;
            }

            return this.calculatePercentWithdrawalFee(this.subTotal, this.feeProcessing.settings)
        },

        currentMaintenanceFee: function () {
            if (this.remainingBalance <= this.maintenanceFee) {
                return this.maintenanceFee;
            }
            return 0;
        },

        currentFee: function () {
            return this.currentWithdrawalFee + this.currentMaintenanceFee;
        },

        showFeeBlock: function () {
            if(this.withdrawalFeeEnabled) {
                return this.canCalculateWithdrawalFee;
            }

            return this.currentMaintenanceFee > 0;
        },

        remainingBalance: function () {
            var result  = (this.balance - this.amount);
            return (result > 0) ? result : 0;
        },

        amountFinal: function () {
            var result  = (this.subTotal - this.currentFee);
            return (result > 0) ? result : 0;
        },

        /**
         * validate amount
         * @returns {*}
         */
        amountError: function () {
            if (this.amount > this.balance) {
                return this.messages.maxAmount;
            }

            if (this.amount < this.minAmount) {
                return this.messages.minAmount;
            }

            /** numbers, dots, without currency **/
            var regular = '^[\\d]+(\\.[\\d]{1,'+this.roundLvl+'})?$';
            if (!this.amount.toString().match(regular)) {
                return this.messages.notValidAmount;
            }

            return '';
        },

        subTotal: function () {
            if (this.amountError) {
                return this.minAmount;
            }

            if (this.amount === this.balance) {
                return this.amount;
            }

            if (this.amount > this.balance) {
                return this.minAmount;
            }

            if (this.amount > (this.balance - this.maintenanceFee)) {
                this.amount = this.balance;
            }

            return this.amount;
        }
    },

    watch: {
        amount: function(val) {
            /**
             * system validate for maintenance fee
             */
            if (this.maintenanceFee && (this.balance - val) <= this.maintenanceFee) {
                val = this.balance;
            }
        },

        balance: function(val) {
            this.checkBalance();
        }
    },

    methods: {

        /**
         * added amount and currency in a message at the end of
         * @param message
         * @returns {string}
         */
        errorWithAmount: function (message) {
            return message + ' ' + this.currencySymbol + this.getAmountValueForError();
        },

        isFloatOrNumber: function (val) {
            return (Number(val) === val && val % 1 === 0)
                || (Number(val) === val && val % 1 !== 0);
        },

        /**
         * get amount for error value
         * @returns {number|*}
         */
        getAmountValueForError: function () {
            var amountString = (this.amountError == this.messages.minAmount)
                ? this.minAmount
                : this.balance;


            /**
             * clear amount for not valid amount error
             * @type {any}
             */
            amountString = (this.amountError == this.messages.notValidAmount)
                ? '' : amountString;

            if (amountString) {

                amountString = ' ' + this.currencySymbol + this.$options.filters.numberStyle(
                    this.$options.filters.truncate.read(amountString, this.roundLvl)
                );

            }

            return amountString;
        },

        checkBalance: function () {
            if (!this.disabled && this.balance < this.minAmount) {
                this.disable();
            } else if(this.disabled && this.balance >= this.minAmount) {
                this.enable();
            }
        },

        disable: function () {
            this.disabled = true;
            this.amount = 0;
        },

        enable: function () {
            this.disabled = false;
            this.amount = this.minAmount;
        },

        updateBalanceViaNode: function (data) {
            if (data && data.hasOwnProperty('av_withdrawal')) {
                this.balance = (data.av_withdrawal > 0) ? data.av_withdrawal : 0;
            }

            if (data && data.hasOwnProperty('equity')) {
                this.balanceWithBonuses = data.equity;
            }

            if (this.showLoader) {
                this.showLoader = false;
            }
        },

        calculatePercentWithdrawalFee: function(amount, settings) {
            var feeMin = parseFloat(settings.minAmount),
                feeMax = parseFloat(settings.maxAmount),
                feePercentage = parseFloat(settings.percent),
                fee = amount / 100 * feePercentage;

            if (fee < feeMin) {
                fee = feeMin;
            } else if (fee > feeMax) {
                fee = feeMax;
            }
            return fee;
        },

        submitWithdrawal: function (event) {
            this.showLoader = true;
        }
    }
});