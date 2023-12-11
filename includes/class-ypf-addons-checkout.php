<?php

class YPF_Addons_Checkout {

    public function __construct() {
        // Constructor code here.
    }

    public function run() {
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));
        // Other hooks and filters.
    }

    public function add_plugin_admin_menu() {
        add_menu_page(
            'General Setting', // Page title
            'YPF Addons', // Menu title
            'manage_options', // Capability
            'ypf-addons-checkout', // Menu slug
            array($this, 'display_plugin_admin_page'), // Function
            'dashicons-admin-generic', // Icon URL
            6 // Position
        );

        add_submenu_page(
            'ypf-addons-checkout', // Parent slug
            'Add-Ons List', // Page title
            'Add-Ons List', // Menu title
            'manage_options', // Capability
            'ypf-addons-list', // Menu slug
            array($this, 'display_addons_list_page') // Function
        );
    }

    public function display_plugin_admin_page() {
        // Include settings page view.
        include_once 'class-ypf-addons-checkout-settings.php';
        $settings_page = new YPF_Addons_Checkout_Settings();
        $settings_page->options_page();
    }

    public function display_addons_list_page() {
        // Code for Add-Ons List page.
    }

    // Other methods.
}
