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

        if (!empty($chosen_addons)) {
            $total = $cart->cart_contents_total; // Get the total

            foreach ($chosen_addons as $addon_id) {
                $addon = $this->get_addon_by_id($addon_id);
                if ($addon) {
                    $addon_fee = ($total * $addon->value_percentage) / 100; // Calculate the percentage fee
                    $display_percentage = (intval($addon->value_percentage) == floatval($addon->value_percentage)) ? intval($addon->value_percentage) : floatval($addon->value_percentage);
                    $fee_name = sprintf(__('%s (+%s%%)', 'ypf-addons-checkout'), $addon->addon_name, $display_percentage);
                    $cart->add_fee($fee_name, $addon_fee, true);
                }
            }
        }
    }

    private function get_addon_by_id($addon_id) {
        global $wpdb;
        // Assume YPF_ADDONS_TABLE_NAME is defined and contains your add-ons
        return $wpdb->get_row($wpdb->prepare("SELECT addon_name, value_percentage FROM " . YPF_ADDONS_TABLE_NAME . " WHERE id = %d", $addon_id));
    }
}

new YPF_Addons_Checkout_Core();
