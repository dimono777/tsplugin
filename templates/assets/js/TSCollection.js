/**
 * Base collection implementation.
 * @param elements
 * @constructor
 */
function TSCollection(elements) {
    this.elements = [];

    if (elements) {
        this.setElements(elements);
    }
}

TSCollection.prototype = {
    /**
     * @param elements
     */
    setElements: function (elements) {
        this.clear();
        this.addAllElements(elements);
        return this;
    },

    getElements: function () {
        return this.elements;
    },

    /**
     * @param element
     */
    addElement: function (element) {
        this.elements.push(element);
        return this;
    },

    /**
     * @param elements
     */
    addAllElements: function (elements) {
        var key;
        if (!elements) {
            return this;
        }
        if (elements instanceof Array || elements instanceof TSCollection) {
            elements.forEach(this.addElement.bind(this));
        } else if (elements instanceof Object) {
            for (key in elements) {
                if (elements.hasOwnProperty(key)) {
                    this.addElement(elements[key]);
                }
            }
        }
        return this;
    },

    /**
     * Clear collection
     */
    clear: function () {
        this.elements = [];
        return this;
    },

    /**
     * @param callback
     */
    filter: function (callback) {
        return new this.constructor(this.elements.filter(callback));
    },

    /**
     * @param callback
     */
    transform: function (callback) {
        return new TSCollection(callback.call(this, this.elements));
    },

    /**
     * @param callback
     */
    map: function (callback) {
        return new TSCollection(this.elements.map(callback));
    },

    /**
     * @param callback
     */
    forEach: function (callback) {
        this.elements.forEach(callback);
        return this;
    },

    /**
     * @param comparatorCallback
     */
    sort: function (comparatorCallback) {
        return new this.constructor(this.elements.sort(comparatorCallback));
    },

    /**
     * @param start
     * @param end
     */
    slice: function (start, end) {
        return new this.constructor(this.elements.slice(start, end));
    },

    /**
     * @param count
     */
    limit: function (count) {
        return this.slice(0, count);
    },

    /**
     * Sort elements by name
     * @param propertyName
     * @param asc
     * @param valueModifier
     */
    orderBy: function (propertyName, asc, valueModifier) {
        asc = typeof asc !== "undefined" ? asc : false;

        return this.sort(function (l, r) {
            if (!l.hasOwnProperty(propertyName) || !r.hasOwnProperty(propertyName)) {
                return 0;
            }
            l = l[propertyName];
            r = r[propertyName];
            if (valueModifier) {
                l = valueModifier.call(this, l);
                r = valueModifier.call(this, r);
            }
            return asc ? l - r : r - l;
        });
    },

    /**
     * Find element by name
     * @param propertyName
     * @param value
     */
    findBy: function (propertyName, value) {
        var i, len;
        len = this.elements.length;
        for (i = 0; i < len; ++i) {
            if (typeof this.elements[i][propertyName] !== "undefined" && this.elements[i][propertyName] === value) {
                return this.elements[i];
            }
        }
        return null;
    }
};
