<?php
/**
 * Plugin Name: YPF Addons Checkout
 * Description: Custom Elementor widget plugin for WooCommerce checkout add-ons.
 * Version: 1.0.1
 * Author: Ardi
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include the main class file.
require_once plugin_dir_path(__FILE__) . 'includes/class-ypf-addons-checkout.php';

function run_ypf_addons_checkout() {
    $plugin = new YPF_Addons_Checkout();
    $plugin->run();
}

run_ypf_addons_checkout();
