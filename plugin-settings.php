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

// Admin menu for plugin settings
add_action( 'admin_menu', 'ypf_addons_checkout_settings_menu' );

function ypf_addons_checkout_settings_menu() {
    add_submenu_page(
        'woocommerce',
        __( 'YPF Add-ons Checkout', 'ypf-addons-checkout' ),
        __( 'Settings', 'ypf-addons-checkout' ),
        'manage_options',
        'ypf-addons-checkout-settings',
        'ypf_addons_checkout_settings_page'
    );
}

// Plugin settings page content
function ypf_addons_checkout_settings_page() {
    echo '<div class="wrap">';
    echo '<h1>' . __( 'YPF Add-ons Checkout Settings', 'ypf-addons-checkout' ) . '</h1>';

    // Title and Description fields
    echo '<form method="post">';
    settings_fields( 'ypf_addons_checkout_settings_group' );
    do_settings_sections( 'ypf-addons-checkout-settings' );
    echo '</form>';

    echo '</div>';
}

// Register settings and fields
add_action( 'admin_init', 'ypf_addons_checkout_register_settings' );

function ypf_addons_checkout_register_settings() {
    register_setting(
        'ypf_addons_checkout_settings_group',
        'ypf_addons_checkout_settings'
    );

    add_settings_section(
        'ypf_addons_checkout_general_section',
        __( 'General Settings', 'ypf-addons-checkout' ),
        'ypf_addons_checkout_general_section_callback',
        'ypf-addons-checkout-settings'
    );

    add_settings_field(
        'ypf_addons_checkout_title',
        __( 'Plugin Title', 'ypf-addons-checkout' ),
        'ypf_addons_checkout_title_field',
        'ypf-addons-checkout-settings',
        'ypf_addons_checkout_general_section'
    );

    add_settings_field(
        'ypf_addons_checkout_description',
        __( 'Plugin Description', 'ypf-addons-checkout' ),
        'ypf_addons_checkout_description_field',
        'ypf-addons-checkout-settings',
        'ypf_addons_checkout_general_section'
    );

    add_settings_field(
        'ypf_addons_checkout_enable',
        __( 'Enable Plugin', 'ypf-addons-checkout' ),
        'ypf_addons_checkout_enable_field',
        'ypf-addons-checkout-settings',
        'ypf_addons_checkout_general_section'
    );
}


// Callback functions for settings sections and fields
function ypf_addons_checkout_general_section_callback() {
    echo '<p>' . __( 'Configure your YPF Add-ons Checkout settings.', 'ypf-addons-checkout' ) . '</p>';
}

function ypf_addons_checkout_title_field() {
    $options = get_option( 'ypf_addons_checkout_settings' );
    $title = $options['ypf_addons_checkout_title'];
    echo '<input type="text" name="ypf_addons_checkout_settings[ypf_addons_checkout_title]" value="' . esc_attr( $title ) . '" class="regular-text" />';
}

function ypf_addons_checkout_description_field() {
    $options = get_option( 'ypf_addons_checkout_settings' );
    $description = $options['ypf_addons_checkout_description'];
    echo '<textarea name="ypf_addons_checkout_settings[ypf_addons_checkout_description]" class="large-text" rows="5">' . esc_attr( $description ) . '</textarea>';
}

function ypf_addons_checkout_enable_field() {
    $options = get_option( 'ypf_addons_checkout_settings' );
    $enabled = $options['ypf_addons_checkout_enable'];
    echo '<input type="checkbox" name="ypf_addons_checkout_settings[ypf_addons_checkout_enable]" ' . checked( 1, $enabled, false ) . ' />';
}
