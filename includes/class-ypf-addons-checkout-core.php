<?php
// class-ypf-addons-checkout-core.php

class YPF_Addons_Checkout_Core {

    public function __construct() {
        // Hook to calculate and add fee at checkout
        add_action('woocommerce_cart_calculate_fees', [$this, 'calculate_addon_fee'], 20, 1);
    }

    public function calculate_addon_fee($cart) {
        if (is_admin() && !defined('DOING_AJAX')) return;

        // This is where you'll get the chosen add-on from the session or POST request
        // For the sake of this example, let's assume the chosen add-on and its percentage are stored in session
        $chosen_addon = WC()->session->get('chosen_addon');
        $addon_percentage = WC()->session->get('chosen_addon_percentage');

        if (!empty($chosen_addon) && !empty($addon_percentage)) {
            $total = $cart->cart_contents_total; // Get the total
            $addon_fee = ($total * $addon_percentage) / 100; // Calculate the percentage fee

            $cart->add_fee(__('Add-On Fee', 'ypf-addons-checkout'), $addon_fee, true);
        }
    }
}

new YPF_Addons_Checkout_Core();
