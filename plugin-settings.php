<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class YPF_Addons_Checkout_Settings {
    
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    public function add_plugin_page() {
        add_menu_page(
            'YPF Addons Checkout', // Page title
            'YPF Addons Checkout', // Menu title
            'manage_options',      // Capability
            'ypf-addons-checkout', // Menu slug
            array( $this, 'create_admin_page' ), // Callback function
            'dashicons-admin-generic', // Icon URL
            25 // Position
        );

        // Submenu page for Add-ons
        add_submenu_page(
            'ypf-addons-checkout', // Parent slug
            'Add-ons', // Page title
            'Add-ons', // Menu title
            'manage_options', // Capability
            'ypf-addons-checkout-addons', // Menu slug
            array( $this, 'addons_page_content' ) // Callback function
        );
    }

    public function create_admin_page() {
        ?>
        <div class="wrap">
            <h2>YPF Addons Checkout</h2>
            <form method="post" action="options.php">
            <?php
                settings_fields( 'ypf_addons_checkout_option_group' );
                do_settings_sections( 'ypf-addons-checkout-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    public function addons_page_content() {
        ?>
        <div class="wrap">
            <h2>Add-ons</h2>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'ypf_addons_checkout_addons_group' );
                do_settings_sections( 'ypf-addons-checkout-addons' );
                submit_button('Add Add-on');
                ?>
            </form>
            <h2>Existing Add-ons</h2>
            <?php $this->list_addons(); // Method to list all addons in a table ?>
        </div>
        <?php
    }

    public function page_init() {
        register_setting(
            'ypf_addons_checkout_option_group', // Option group
            'ypf_addons_checkout_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Settings', // Title
            null, // Callback
            'ypf-addons-checkout-admin' // Page
        );  

        add_settings_field(
            'enable', // ID
            'Enable', // Title 
            array( $this, 'enable_callback' ), // Callback
            'ypf-addons-checkout-admin', // Page
            'setting_section_id' // Section           
        );

        // Add more fields for add-on creation here
    }

    public function sanitize($input) {
        $new_input = array();
        if(isset($input['enable']))
            $new_input['enable'] = absint($input['enable']);

        // Sanitize other input data

        return $new_input;
    }

    public function enable_callback() {
        $options = get_option( 'ypf_addons_checkout_options' );
        echo '<input type="checkbox" id="enable" name="ypf_addons_checkout_options[enable]" value="1"' . checked( 1, isset( $options['enable'] ) ? $options['enable'] : 0, false ) . '/>';
    }

    // Callbacks for add-on fields go here

}

// If you're planning to submit the form data, you'll need to add appropriate form handling logic.
