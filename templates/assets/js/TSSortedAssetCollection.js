function TSSortedAssetCollection(delegate, sortingProperty, asc, valueModifier) {
    var getElements = delegate.getElements;
    asc = typeof asc === "undefined" ? 0 : asc;

    delegate.getElements = function () {
        return getElements.call(delegate
            .orderBy(sortingProperty, asc, valueModifier));
    };

    return delegate;
}
