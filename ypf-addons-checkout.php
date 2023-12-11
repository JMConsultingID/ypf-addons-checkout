<?php
/*
Plugin Name: YPF Addons Checkout
Description: A plugin to create add-ons fee and calculation on the WooCommerce checkout, with Elementor widget support.
Version: 1.0.1
Author: Ardi
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class YPF_Addons_Checkout {

    // Constructor
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'create_settings_page' ) );
        add_action( 'admin_init', array( $this, 'setup_settings' ) );
    }

    // Create settings page
    public function create_settings_page() {
        add_menu_page(
            'YPF Addons Checkout Settings',
            'YPF Addons Checkout',
            'manage_options',
            'ypf-addons-checkout',
            array( $this, 'settings_page_content' ),
            'dashicons-admin-generic',
            65
        );
    }

    // Settings page content
    public function settings_page_content() {
        ?>
        <div class="wrap">
            <h1>YPF Addons Checkout Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'ypf-addons-checkout-options' );
                do_settings_sections( 'ypf-addons-checkout' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // Setup settings
    public function setup_settings() {
        register_setting( 'ypf-addons-checkout-options', 'ypf_addons_checkout_enabled' );

        add_settings_section(
            'ypf_addons_checkout_section',
            'Enable/Disable Plugin',
            null,
            'ypf-addons-checkout'
        );

        add_settings_field(
            'ypf_addons_checkout_enabled', 
            'Enable Plugin', 
            array( $this, 'checkbox_field_callback' ), 
            'ypf-addons-checkout', 
            'ypf_addons_checkout_section',
            array( 'label_for' => 'ypf_addons_checkout_enabled' )
        );
    }

    // Checkbox field callback
    public function checkbox_field_callback() {
        $enabled = get_option( 'ypf_addons_checkout_enabled' );
        $checked = isset( $enabled ) ? checked( $enabled, '1', false ) : '';
        echo "<input type='checkbox' id='ypf_addons_checkout_enabled' name='ypf_addons_checkout_enabled' value='1' $checked />";
    }
}

new YPF_Addons_Checkout();
