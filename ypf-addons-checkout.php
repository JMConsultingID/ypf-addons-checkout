<?php
/*
Plugin Name: YPF Addons Checkout
Description: A plugin to create add-ons fee and calculation on the WooCommerce checkout.
Version: 1.0.1
Author: Ardi
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Include the plugin settings file
require_once plugin_dir_path( __FILE__ ) . 'plugin-settings.php';

// Activation hook to create the database table
register_activation_hook(__FILE__, 'ypf_addons_checkout_install');

// Function to create a new table for addon fees
function ypf_addons_checkout_install() {
    // We will define this function in plugin-settings.php
    ypf_addons_checkout_create_db_table();
}

// Initialize the settings page class
$ypf_addons_checkout_settings_page = new YPF_Addons_Checkout_Settings();
