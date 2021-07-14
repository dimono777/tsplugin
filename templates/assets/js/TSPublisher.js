/**
 * @constructor
 */
function TSPublisher() {
    this._subscribers = [];
}

TSPublisher.prototype = {
    subscribe: function (subscriber) {
        this._subscribers.push(subscriber);
        return this;
    },

    notify: function (data) {
        this._subscribers.forEach(function (subscriber) {
            subscriber.update(data)
        });
        return this;
    }
};
