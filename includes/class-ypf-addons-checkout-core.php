<?php
// class-ypf-addons-checkout-core.php

class YPF_Addons_Checkout_Core {

    public function __construct() {
        // Hook to calculate and add fee at checkout
        add_action('woocommerce_cart_calculate_fees', [$this, 'calculate_addon_fee'], 20, 1);
    }

    public function calculate_addon_fee($cart) {
        if (is_admin() && !defined('DOING_AJAX')) return;

        // Retrieve the chosen add-on ID and percentage from the session
        $chosen_addon_id = WC()->session->get('chosen_addon');
        $addon_percentage = WC()->session->get('chosen_addon_percentage');

        if (!empty($chosen_addon_id) && !empty($addon_percentage)) {
            // Retrieve the add-on name from the database
            $addon_name = $this->get_addon_name_by_id($chosen_addon_id);
            $total = $cart->cart_contents_total; // Get the total
            $addon_fee = ($total * $addon_percentage) / 100; // Calculate the percentage fee

            // Append " - Add-On Fee" and the percentage to the add-on name and add the fee to the cart
            $display_percentage = (intval($addon_percentage) == floatval($addon_percentage)) ? intval($addon_percentage) : floatval($addon_percentage);
            $fee_name = sprintf(__('%s (+%s%%) - Add-On Fee', 'ypf-addons-checkout'), $addon_name, $display_percentage);
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
