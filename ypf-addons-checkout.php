<?php
/*
Plugin Name: YPF Addons Checkout
Description: A plugin to create add-ons fee and calculation on the WooCommerce checkout, with Elementor widget support.
Version: 1.0.1
License: GPLv2 or later
Text Domain: ypf-addons-checkout
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
        register_setting( 'ypf-addons-checkout-options', 'ypf_addons_checkout_settings' );

        add_settings_section(
            'ypf_addons_checkout_section',
            'Settings',
            null,
            'ypf-addons-checkout'
        );

        add_settings_field(
            'ypf_addons_checkout_title', 
            'Title', 
            array( $this, 'text_field_callback' ), 
            'ypf-addons-checkout', 
            'ypf_addons_checkout_section',
            array( 'label_for' => 'ypf_addons_checkout_title' )
        );

        add_settings_field(
            'ypf_addons_checkout_description', 
            'Description', 
            array( $this, 'textarea_field_callback' ), 
            'ypf-addons-checkout', 
            'ypf_addons_checkout_section',
            array( 'label_for' => 'ypf_addons_checkout_description' )
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

    // Text field callback
    public function text_field_callback( $args ) {
        $options = get_option( 'ypf_addons_checkout_settings' );
        $field = $args['label_for'];
        $value = isset( $options[$field] ) ? esc_attr( $options[$field] ) : '';
        echo "<input type='text' id='$field' name='ypf_addons_checkout_settings[$field]' value='$value' />";
    }

    // Textarea field callback
    public function textarea_field_callback( $args ) {
        $options = get_option( 'ypf_addons_checkout_settings' );
        $field = $args['label_for'];
        $value = isset( $options[$field] ) ? esc_textarea( $options[$field] ) : '';
        echo "<textarea id='$field' name='ypf_addons_checkout_settings[$field]'>$value</textarea>";
    }

    // Checkbox field callback
    public function checkbox_field_callback( $args ) {
        $options = get_option( 'ypf_addons_checkout_settings' );
        $field = $args['label_for'];
        $checked = isset( $options[$field] ) ? checked( $options[$field], 1, false ) : '';
        echo "<input type='checkbox' id='$field' name='ypf_addons_checkout_settings[$field]' value='1' $checked />";
    }
}

new YPF_Addons_Checkout();
