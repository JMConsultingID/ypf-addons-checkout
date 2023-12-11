<?php
/*
Plugin Name: YPF Addons Checkout
Description: A plugin to create add-ons fee and calculation on the WooCommerce checkout, with Elementor widget support.
Version: 1.0.1
Author: Your Name
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Include the plugin settings file
require_once plugin_dir_path( __FILE__ ) . 'plugin-settings.php';

// Activation hook for creating database tables and initializing settings
register_activation_hook(__FILE__, 'ypf_addons_checkout_activate');

function ypf_addons_checkout_activate() {
    // Code to set up database tables (not shown here)
    // You can call the same function used to create tables here
}

// Initialize the plugin settings
$ypf_addons_checkout_settings = new YPF_Addons_Checkout_Settings();
