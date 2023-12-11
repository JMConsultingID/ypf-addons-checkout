<?php

class YPF_Addons_Checkout {

    /**
     * Singleton instance.
     */
    private static $instance = null;

    /**
     * Get the instance of this class.
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
    }

    /**
     * Add settings page.
     */
    public function add_plugin_page() {
        add_menu_page(
            'YPF Add-Ons Settings',       // Page title
            'YPF Add-Ons',       // Menu title
            'manage_options',           // Capability
            'ypf-addons-checkout',      // Menu slug
            array( $this, 'create_admin_page' ), // Callback function
            'dashicons-admin-plugins',  // Icon
            110                         // Position
        );

        add_submenu_page(
            'ypf-addons-checkout',      // Parent slug
            'Add-Ons List',             // Page title
            'Add-Ons List',             // Menu title
            'manage_options',           // Capability
            'ypf-addons-list',          // Menu slug
            array( $this, 'add_ons_list_page' )  // Callback function
        );
    }

    /**
     * Admin settings page content.
     */
    public function create_admin_page() {
        ?>
        <div class="wrap">
            <h1>YPF Addons Checkout Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'ypf_addons_group' );
                do_settings_sections( 'ypf-addons-checkout' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Add-Ons List Page.
     */
    public function add_ons_list_page() {
        // Content for Add-Ons List page.
        echo '<div class="wrap"><h1>Add-Ons List</h1></div>';
    }
}

