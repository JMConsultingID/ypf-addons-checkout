jQuery(document).ready(function($) {
    // Store the last selected radio button
    var lastChecked = $('input[type="radio"][name="ypf_addon"]:checked')[0];

    if (lastChecked) {
        var addonId = $(lastChecked).val();
        var addonPercentage = $(lastChecked).data('value');

        // AJAX request to update session with the selected add-on
        $.ajax({
            type: 'POST',
            url: ypf_addons_ajax.ajax_url,
            data: {
                action: 'update_selected_addon',
                addon_id: addonId,
                addon_percentage: addonPercentage,
                nonce: ypf_addons_ajax.nonce
            },
            success: function(response) {
                // Trigger an update in the checkout totals
                $(document.body).trigger('update_checkout');
            }
        });
    }

    $('input[type="radio"][name="ypf_addon"]').on('click', function() {
        if (this === lastChecked) {
            $(this).prop('checked', false);
            lastChecked = null;
        } else {
            lastChecked = this;
        }

        var addonId = $(this).is(':checked') ? $(this).val() : '';
        var addonPercentage = $(this).is(':checked') ? $(this).data('value') : 0;

        // AJAX request to update session with the selected add-on or remove it
        $.ajax({
            type: 'POST',
            url: ypf_addons_ajax.ajax_url,
            data: {
                action: 'update_selected_addon',
                addon_id: addonId,
                addon_percentage: addonPercentage,
                nonce: ypf_addons_ajax.nonce
            },
            success: function(response) {
                // Trigger an update in the checkout totals
                $(document.body).trigger('update_checkout');
            }
        });
    });
});