<?php
/**
 * Plugin Name: YPF Addons Checkout
 * Plugin URI: https://example.com/ypf-addons-checkout/
 * Description: Adds custom add-ons fees and calculations to WooCommerce checkout using Elementor widgets.
 * Version: 1.0.1
 * Author: Ardi
 * Author URI: https://example.com/
 * License: GPLv2 or later
 * Text Domain: ypf-addons-checkout
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

add_action( 'admin_menu', 'ypf_addons_checkout_menu' );

function ypf_addons_checkout_menu() {
    add_menu_page( 
        'YPF Addons Product', 
        'YPF Addons Product', 
        'manage_options', 
        'ypf-addons-product-settings', 
        'ypf_addons_checkout_settings_page', 
        null, 
        99 
    );

    add_submenu_page(
        'ypf-addons-product-settings',
        'Add-Ons List',
        'Add-Ons List',
        'manage_options',
        'ypf-addons-list',
        'ypf_addons_list_page'
    );
}

function ypf_addons_checkout_settings_page(){
    ?>
    <!-- HTML for settings page -->
    <div class="wrap">
        <h1>YPF Addons Checkout Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'ypf-addons-checkout-settings' );
            do_settings_sections( 'ypf-addons-checkout-settings' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function ypf_addons_list_page(){
    // Add your content for the Add-Ons List page here
}

add_action( 'admin_init', 'ypf_addons_checkout_settings_init' );

function ypf_addons_checkout_settings_init() {
    register_setting( 'ypf-addons-checkout-settings', 'ypf_addons_checkout_enabled' );

    add_settings_section(
        'ypf_addons_checkout_settings_section',
        'YPF Addons Checkout Settings',
        'ypf_addons_checkout_settings_section_cb',
        'ypf-addons-checkout-settings'
    );

    add_settings_field(
        'ypf_addons_checkout_enable',
        'Enable YPF Addons Checkout',
        'ypf_addons_checkout_enable_cb',
        'ypf-addons-checkout-settings',
        'ypf_addons_checkout_settings_section'
    );
}

function ypf_addons_checkout_settings_section_cb() {
    echo '<p>Enable or Disable the YPF Addons Checkout.</p>';
}

function ypf_addons_checkout_enable_cb() {
    $option = get_option( 'ypf_addons_checkout_enabled' );
    echo '<input type="checkbox" id="ypf_addons_checkout_enabled" name="ypf_addons_checkout_enabled" value="1" ' . checked( 1, $option, false ) . '/>';
}
