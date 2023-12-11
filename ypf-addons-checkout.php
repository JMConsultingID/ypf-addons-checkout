<?php
/**
 * Plugin Name: YPF Addons Checkout
 * Description: Adds an additional fee at checkout in WooCommerce.
 * Version: 1.0.1
 * Author: Ardi
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin paths and URLs.
define( 'YPF_ADDONS_CHECKOUT_PATH', plugin_dir_path( __FILE__ ) );
define( 'YPF_ADDONS_CHECKOUT_URL', plugin_dir_url( __FILE__ ) );

// Include the main class.
require_once YPF_ADDONS_CHECKOUT_PATH . 'includes/class-ypf-addons-checkout.php';

// Initialize the plugin.
YPF_Addons_Checkout::instance();
