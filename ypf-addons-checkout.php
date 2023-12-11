<?php
/**
 * Plugin Name: YPF Add-ons Checkout
 * Plugin URI: https://yourpropfirm.com/
 * Description: Add custom add-ons and fees to your WooCommerce checkout.
 * Author: Ardi
 * Version: 1.0.1
 * License: GPLv2 or later
 * Text Domain: ypf-addons-checkout
 */

// Check WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) ) {
  return;
}

// Include plugin files
// require_once( 'includes/class-ypf-addons-checkout.php' );
require_once( 'plugin-settings.php' ); // Include settings file

// Register activation and deactivation hooks
register_activation_hook( __FILE__, 'ypf_addons_checkout_activation' );
register_deactivation_hook( __FILE__, 'ypf_addons_checkout_deactivation' );

// Initialize the plugin
$ypf_addons_checkout = new YPF_Addons_Checkout();
