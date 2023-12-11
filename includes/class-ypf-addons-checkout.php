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
                settings_fields( 'ypf_addons_option_group' );
                do_settings_sections( 'ypf-addons-checkout-admin' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings.
     */
    public function page_init() {
        register_setting(
            'ypf_addons_option_group', // Option group
            'ypf_addons_option_name',  // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id',        // ID
            'Settings',                  // Title
            array( $this, 'print_section_info' ), // Callback
            'ypf-addons-checkout-admin'  // Page
        );  

        add_settings_field(
            'enable',                    // ID
            'Enable',                    // Title 
            array( $this, 'enable_callback' ),   // Callback
            'ypf-addons-checkout-admin', // Page
            'setting_section_id'         // Section           
        );      
    }

    /**
     * Sanitize each setting field as needed.
     */
    public function sanitize( $input ) {
        $new_input = array();
        if( isset( $input['enable'] ) )
            $new_input['enable'] = absint( $input['enable'] );

        return $new_input;
    }

    /**
     * Print the Section text.
     */
    public function print_section_info() {
        print 'Change the settings below:';
    }

    /**
     * Get the settings option array and print one of its values.
     */
    public function enable_callback() {
        $options = get_option( 'ypf_addons_option_name' );
        ?>
        <input type="checkbox" id="enable" name="ypf_addons_option_name[enable]" value='1' <?php checked( 1, isset( $options['enable'] ) ? $options['enable'] : 0 ); ?> />
        <label for="enable">Enable YPF Addons Checkout</label>
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

