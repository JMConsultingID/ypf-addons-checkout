<?php
// class-ypf-addons-checkout-core.php

class YPF_Addons_Checkout_Core {

    public function __construct() {
        // Hook to calculate and add fee at checkout
        add_action('woocommerce_cart_calculate_fees', [$this, 'calculate_addon_fee'], 20, 1);
    }

    public function calculate_addon_fee($cart) {
        if (is_admin() && !defined('DOING_AJAX')) return;

        // Retrieve the chosen add-ons and their total percentage from the session
        $chosen_addons = WC()->session->get('chosen_addons', array());
        $total_addon_percentage = WC()->session->get('chosen_addons_percentage', 0);

        if (!empty($chosen_addons) && !empty($total_addon_percentage)) {
            $total = $cart->cart_contents_total; // Get the total
            $addon_fee = ($total * $total_addon_percentage) / 100; // Calculate the percentage fee

            // Construct the fee name from all chosen add-ons
            $addon_names = array_map([$this, 'get_addon_name_by_id'], $chosen_addons);
            $addon_names_str = implode(', ', $addon_names);
            $display_percentage = (intval($total_addon_percentage) == floatval($total_addon_percentage)) ? intval($total_addon_percentage) : floatval($total_addon_percentage);
            $fee_name = sprintf(__('Add-Ons: %s (+%s%%)', 'ypf-addons-checkout'), $addon_names_str, $display_percentage);

            // Add the fee to the cart
            $cart->add_fee($fee_name, $addon_fee, true);
        }
    }

    private function get_addon_name_by_id($addon_id) {
        global $wpdb;
        // Assume YPF_ADDONS_TABLE_NAME is defined and contains your add-ons
        $addon = $wpdb->get_row($wpdb->prepare("SELECT addon_name FROM " . YPF_ADDONS_TABLE_NAME . " WHERE id = %d", $addon_id));
        return $addon ? $addon->addon_name : '';
    }
}

new YPF_Addons_Checkout_Core();
