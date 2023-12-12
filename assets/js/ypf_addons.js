jQuery(document).ready(function($) {
    $('input[name="ypf_addon"]').on('change', function() {
        var addonId = $(this).val(); // Get the selected add-on ID
        var addonPercentage = $(this).data('value'); // Assume you have data-percentage attribute in your radio buttons
        
        // AJAX request to update session with the selected add-on
        $.ajax({
            type: 'POST',
            url: ypf_addons_ajax.ajax_url,
            data: {
                action: 'update_selected_addon',
                addon_id: addonId,
                addon_percentage: addonPercentage
            },
            success: function(response) {
                // You may want to trigger an update in the checkout totals
                $(document.body).trigger('update_checkout');
            }
        });
    });
});
