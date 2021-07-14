function RedirectAfterAction()
{

    var selector = {
        'customElements': '.custom_elements'
    };

    // Change select option
    this.changeSelectOption = function(t)
    {
        // Current select
        var select = jQuery(t);

        // Active option of current select
        var activeOption = jQuery(":selected", select);

        // Depending on the class of the group, perform the necessary actions
        switch (activeOption.parent().attr('id')) {
            case 'custom_group':
                this.showCustomElement(activeOption);
                break;
            default:
                this.hideCustomElements(select);
                break;
        }
    };

    /**
     * Show custom element
     * @param activeOption
     */
    this.showCustomElement = function(activeOption)
    {
        var customElementId = activeOption.attr('custom_element_id');

        jQuery("#" + customElementId).show();
    };

    /**
     * Hide custom elements
     * @param select
     */
    this.hideCustomElements = function(select)
    {
        select.next().children().hide();
    }
}