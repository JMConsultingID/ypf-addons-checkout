jQuery(document).ready(function($) {
    // Function to update selected addons
    function updateSelectedAddons() {
        var chosen_addons = [];
        var chosen_addons_percentage = 0;

        $('input[type="checkbox"][name="ypf_addons[]"]:checked').each(function() {
            chosen_addons.push($(this).val());
            chosen_addons_percentage += parseFloat($(this).data('value'));
        });

        // AJAX request to update session with the selected add-ons
        $.ajax({
            type: 'POST',
            url: ypf_addons_ajax.ajax_url,
            data: {
                action: 'update_selected_addons',
                addons: chosen_addons,
                addons_percentage: chosen_addons_percentage,
                nonce: ypf_addons_ajax.nonce
            },
            success: function(response) {
                // Trigger an update in the checkout totals
                $(document.body).trigger('update_checkout');
            }
        });
    }

    // Initialize selected addons on page load
    updateSelectedAddons();

    // Event listener for checkbox changes
    $('input[type="checkbox"][name="ypf_addons[]"]').on('change', function() {
        updateSelectedAddons();
    });
});
