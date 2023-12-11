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

global $wpdb;
define( 'YPF_ADDONS_TABLE_NAME', $wpdb->prefix . 'ypf_addons' );

register_activation_hook( __FILE__, 'ypf_addons_create_table' );

function ypf_addons_create_table() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE " . YPF_ADDONS_TABLE_NAME . " (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        addon_name varchar(255) NOT NULL,
        value_percentage decimal(5,2) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
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

    add_submenu_page(
        'ypf-addons-rules-settings',
        'Add-Ons Rule',
        'Add-Ons Rule',
        'manage_options',
        'ypf-addons-rule',
        'ypf_addons_rule_page'
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

function ypf_addons_rule_page(){

}

function ypf_addons_list_page(){
    global $wpdb;

    // Handle form submission
    if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
        $addon_name = sanitize_text_field( $_POST['addon_name'] );
        $value_percentage = floatval( $_POST['value_percentage'] );

        $wpdb->insert(
            YPF_ADDONS_TABLE_NAME,
            array(
                'addon_name' => $addon_name,
                'value_percentage' => $value_percentage
            )
        );
    }

    // Retrieve data from the database
    $addons = $wpdb->get_results( "SELECT * FROM " . YPF_ADDONS_TABLE_NAME );

    // HTML form
    ?>
    <div class="wrap">
        <h1>Add New Add-On Fee</h1>
        <form method="post" action="">
            <!-- Form Fields -->
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Add-On Name:</th>
                    <td><input type="text" name="addon_name" value="" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Value (Percentage):</th>
                    <td><input type="text" name="value_percentage" value="" /></td>
                </tr>
            </table>

            <?php submit_button('Add Add-On'); ?>
        </form>
        
        <!-- Display Data in a Table -->
        <h2>Add-Ons List</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Add-On Name</th>
                    <th scope="col">Value (Percentage)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $addons as $addon ) : ?>
                    <tr>
                        <td><?php echo $addon->id; ?></td>
                        <td><?php echo esc_html( $addon->addon_name ); ?></td>
                        <td><?php echo esc_html( $addon->value_percentage ); ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
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
