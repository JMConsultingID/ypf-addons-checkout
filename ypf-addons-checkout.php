<?php
/**
 * Plugin Name: YPF Addons Checkout
 * Plugin URI: https://yourpropfirm.com/
 * Description: Adds custom add-ons fees and calculations to WooCommerce checkout using Elementor widgets.
 * Version: 1.0.1
 * Author: Ardi
 * License: GPLv2 or later
 * Text Domain: ypf-addons-checkout
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

global $wpdb;
define( 'YPF_ADDONS_TABLE_NAME', $wpdb->prefix . 'ypf_addons' );

register_activation_hook( __FILE__, 'ypf_addons_create_table' );

function ypf_addons_delete_table() {
    global $wpdb;
    $table_name = YPF_ADDONS_TABLE_NAME;
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
}

function ypf_addons_create_table() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE " . YPF_ADDONS_TABLE_NAME . " (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        addon_name varchar(255) NOT NULL,
        value_percentage decimal(5,2) NOT NULL,
        ypf_parameter varchar(50) NOT NULL,
        ypf_parameter_value mediumint(9) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

// Define constants for paths used in the plugin
define( 'YPF_ADDONS_CHECKOUT_PATH', plugin_dir_path( __FILE__ ) );
define( 'YPF_ADDONS_CHECKOUT_URL', plugin_dir_url( __FILE__ ) );

// Include the Elementor widget class file
function ypf_addons_checkout_include_widgets() {
    if ( did_action( 'elementor/loaded' ) ) {
        require_once( YPF_ADDONS_CHECKOUT_PATH . 'includes/class-ypf-addons-checkout-elementor.php' );
        require_once( YPF_ADDONS_CHECKOUT_PATH . 'includes/class-ypf-addons-checkout-core.php' );
    }
}
add_action( 'plugins_loaded', 'ypf_addons_checkout_include_widgets' );

add_action( 'admin_menu', 'ypf_addons_checkout_menu' );

function ypf_addons_checkout_menu() {
    add_menu_page( 
        'YPF Settings', 
        'YPF Settings', 
        'manage_options', 
        'ypf-addons-product-settings', 
        'ypf_addons_checkout_settings_page', 
        null, 
        25
    );

    add_submenu_page(
        'ypf-addons-product-settings',
        'YPF Add-Ons List',
        'YPF Add-Ons List',
        'manage_options',
        'ypf-addons-list',
        'ypf_addons_list_page'
    );

    add_submenu_page(
        'ypf-addons-product-settings',
        'Add-Ons Rule',
        'Add-Ons Rule',
        'manage_options',
        'ypf-addons-rule',
        'ypf_addons_rule_page'
    );

    add_submenu_page(
        'ypf-addons-product-settings',
        'Regenerate Database',
        'Regenerate Database',
        'manage_options',
        'ypf-regenerate-table',
        'ypf_regenerate_table_page'
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
    ?>
    <!-- HTML for settings page -->
    <div class="wrap">
        <h1>YPF Addons Rule to Connect ProgramID Dashboard</h1>
        <p>The feature you're trying to access is currently under active development.<br>We are making steady progress and are excited to bring it to you soon. Stay tuned for updates in upcoming releases, <br>and we appreciate your patience and interest in our evolving product. <br>For any inquiries or feedback regarding this feature, please feel free to contact our support team</p>
    </div>
    <?php
}

function ypf_regenerate_table_page(){
    ?>
    <div class="wrap">
        <h1>YPF Regenerate Table Addons</h1>
        <form method="post" action="">
            <input type="hidden" name="ypf_regenerate_table" value="1">
            <button type="submit" class="button button-primary">Regenerate Database Table</button>
        </form>
    </div>
    <?php

    // Jika form disubmit, panggil fungsi pembuatan ulang tabel
    if (isset($_POST['ypf_regenerate_table']) && $_POST['ypf_regenerate_table'] == '1') {
        ypf_addons_delete_table();
        ypf_addons_create_table();
        echo '<div class="notice notice-success"><p>Database table regenerated successfully.</p></div>';
    }
}

function ypf_addons_list_page(){
    global $wpdb;

    $edit = false;
    $addon_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $addon_name = '';
    $value_percentage = '';
    $profit_split = '';
    $withdraw_active_days = '';
    $withdraw_trading_days = '';

    // Check if in edit mode
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && $addon_id > 0) {
        $edit = true;
        $addon = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . YPF_ADDONS_TABLE_NAME . " WHERE id = %d", $addon_id ) );
        if ($addon) {
            $addon_name = $addon->addon_name;
            $value_percentage = $addon->value_percentage;
            $profit_split = $addon->profit_split;
            $withdraw_active_days = $addon->withdraw_active_days;
            $withdraw_trading_days = $addon->withdraw_trading_days;
        }
    }

    // Check if in delete mode and handle deletion
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $addon_id = intval($_GET['id']);

        // Perform deletion
        $wpdb->delete(YPF_ADDONS_TABLE_NAME, array('id' => $addon_id));

        // Redirect back to the Add-Ons List page
        wp_redirect(admin_url('admin.php?page=ypf-addons-list'));
        exit;
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $submitted_addon_name = sanitize_text_field($_POST['addon_name']);
        $submitted_value_percentage = floatval($_POST['value_percentage']);
        $submitted_profit_split = floatval($_POST['profit_split']);
        $submitted_withdraw_active_days = floatval($_POST['withdraw_active_days']);
        $submitted_withdraw_trading_days = floatval($_POST['withdraw_trading_days']);

        if ($edit) {
            // Update existing add-on
            $wpdb->update(
                YPF_ADDONS_TABLE_NAME,
                array('addon_name' => $submitted_addon_name, 
                      'value_percentage' => $submitted_value_percentage,
                      'profit_split' => $submitted_profit_split,
                      'withdraw_active_days' => $submitted_withdraw_active_days,
                      'withdraw_trading_days' => $submitted_withdraw_trading_days
                  ),
                array('id' => $addon_id)
            );
        } else {
            // Insert new add-on
            $wpdb->insert(
                YPF_ADDONS_TABLE_NAME,
                array('addon_name' => $submitted_addon_name, 
                      'value_percentage' => $submitted_value_percentage,
                      'profit_split' => $submitted_profit_split,
                      'withdraw_active_days' => $submitted_withdraw_active_days,
                      'withdraw_trading_days' => $submitted_withdraw_trading_days
                  ),
            );
        }
        // Redirect back to the Add-Ons List page
        wp_redirect(admin_url('admin.php?page=ypf-addons-list'));
        exit;
    }

    // Retrieve data from the database for listing
    $addons = $wpdb->get_results( "SELECT * FROM " . YPF_ADDONS_TABLE_NAME );

    // HTML Form
    ?>
    <div class="wrap">
        <h1><?php echo $edit ? 'Edit' : 'Add New'; ?> Add-On</h1>
        <form method="post" action="">
            <table class="form-table" style="border-collapse: collapse; width: 100%; margin-top: 20px;">
                <tr>
                    <th scope="row" style="border: 1px solid #ddd; padding: 8px;">Add-On Name:</th>
                    <th scope="row" style="border: 1px solid #ddd; padding: 8px;">Fee Add-ons (Percentage):</th>
                    <th scope="row" style="border: 1px solid #ddd; padding: 8px;">Profit Split:</th>
                    <th scope="row" style="border: 1px solid #ddd; padding: 8px;">Withdraw Active Days:</th>
                    <th scope="row" style="border: 1px solid #ddd; padding: 8px;">Withdraw Trading Days:</th>
                    <th scope="row" style="border: 1px solid #ddd; padding: 8px;"></th> <!-- Empty for the button -->
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">
                        <input type="text" name="addon_name" value="<?php echo esc_attr($addon_name); ?>" style="width: 100%;" />
                    </td>
                    <td style="border: 1px solid #ddd; padding: 8px;">
                        <input type="text" name="value_percentage" value="<?php echo esc_attr($value_percentage ?: '0'); ?>" style="width: 100%;" />
                    </td>
                    <td style="border: 1px solid #ddd; padding: 8px;">
                        <input type="text" name="profit_split" value="<?php echo esc_attr($profit_split ?: '0'); ?>" style="width: 100%;" />
                    </td>
                    <td style="border: 1px solid #ddd; padding: 8px;">
                        <input type="text" name="withdraw_active_days" value="<?php echo esc_attr($withdraw_active_days ?: '0'); ?>" style="width: 100%;" />
                    </td>
                    <td style="border: 1px solid #ddd; padding: 8px;">
                        <input type="text" name="withdraw_trading_days" value="<?php echo esc_attr($withdraw_trading_days ?: '0'); ?>" style="width: 100%;" />
                    </td>
                    <td style="border: 1px solid #ddd; padding: 8px;">
                        <?php submit_button($edit ? 'Update Add-On' : 'Add Add-On', 'primary', 'submit', false); ?>
                    </td>
                </tr>
            </table>
        </form>
        
        <!-- Display Data in a Table -->
        <h2>Add-Ons List</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Add-On Name</th>
                    <th scope="col">Fee Add-ons (Percentage)</th>                    
                    <th scope="col">Profit Split</th>
                    <th scope="col">Withdraw Active Days</th>
                    <th scope="col">Withdraw Trading Days</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $i=1; ?>
                <?php foreach( $addons as $addon ) : ?>
                    <tr>
                        <td><?php echo esc_html( $addon->id ); ?></td>
                        <td><?php echo esc_html( $addon->addon_name ); ?></td>
                        <td><?php echo esc_html( $addon->value_percentage ); ?>%</td>
                        <td><?php echo esc_html( $addon->profit_split ); ?></td>
                        <td><?php echo esc_html( $addon->withdraw_active_days ); ?></td>
                        <td><?php echo esc_html( $addon->withdraw_trading_days ); ?></td>
                        <td>
                            <a href="<?php echo admin_url( 'admin.php?page=ypf-addons-list&action=edit&id=' . $addon->id ); ?>">Edit</a> | 
                            <a href="<?php echo admin_url( 'admin.php?page=ypf-addons-list&action=delete&id=' . $addon->id ); ?>" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                        </td>
                    </tr>
                    <?php $i++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

add_action( 'admin_init', 'ypf_addons_checkout_settings_init' );

function ypf_addons_checkout_settings_init() {
    register_setting( 'ypf-addons-checkout-settings', 'ypf_addons_checkout_enabled' );
    register_setting( 'ypf-addons-checkout-settings', 'ypf_addons_checkout_title' );
    register_setting( 'ypf-addons-checkout-settings', 'ypf_addons_checkout_default_id' );

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

    // Add a new field for the title of the add-ons
    add_settings_field(
        'ypf_addons_checkout_title',
        'Add-Ons Title',
        'ypf_addons_checkout_title_cb',
        'ypf-addons-checkout-settings',
        'ypf_addons_checkout_settings_section'
    );

    // Add a Default Addons ID 
    add_settings_field(
        'ypf_addons_checkout_default_id',
        'Default Add-Ons id',
        'ypf_addons_checkout_default_id_cb',
        'ypf-addons-checkout-settings',
        'ypf_addons_checkout_settings_section'
    );
}

function ypf_addons_checkout_settings_section_cb() {
    echo '<p>Configure the settings for YPF Addons Checkout.</p>';
}

function ypf_addons_checkout_enable_cb() {
    $option = get_option( 'ypf_addons_checkout_enabled' );
    echo '<input type="checkbox" id="ypf_addons_checkout_enabled" name="ypf_addons_checkout_enabled" value="1" ' . checked( 1, $option, false ) . '/>';
}

// Callback function for the add-ons title field
function ypf_addons_checkout_title_cb() {
    $title = get_option('ypf_addons_checkout_title');
    echo '<input type="text" id="ypf_addons_checkout_title" name="ypf_addons_checkout_title" value="' . esc_attr($title) . '" />';
}

// Callback function for the add-ons title field
function ypf_addons_checkout_default_id_cb() {
    $addons_default_id = get_option('ypf_addons_checkout_default_id');
    echo '<input type="text" id="ypf_addons_checkout_default_id" name="ypf_addons_checkout_default_id" value="' . esc_attr($addons_default_id) . '" />';
}


function ypf_addons_enqueue_scripts() {
    wp_enqueue_script('ypf-addons-script', plugin_dir_url(__FILE__) . 'assets/js/ypf_addons.js', array('jquery'), null, true);
    wp_localize_script('ypf-addons-script', 'ypf_addons_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ypf_addons_nonce') // Nonce for security
    ));
}
add_action('wp_enqueue_scripts', 'ypf_addons_enqueue_scripts');

// Hook for the AJAX action
add_action('wp_ajax_update_selected_addon', 'ypf_addons_update_selected_addon');
add_action('wp_ajax_nopriv_update_selected_addon', 'ypf_addons_update_selected_addon'); // If needed for non-logged in users

function ypf_addons_update_selected_addon() {
    check_ajax_referer('ypf_addons_nonce', 'nonce'); // Security check

    $addon_id = isset($_POST['addon_id']) ? sanitize_text_field($_POST['addon_id']) : '';
    $addon_percentage = isset($_POST['addon_percentage']) ? floatval($_POST['addon_percentage']) : 0;

    WC()->session->set('chosen_addon', $addon_id);
    WC()->session->set('chosen_addon_percentage', $addon_percentage);

    wp_send_json_success();
}

function get_addons_data_default() {
    global $wpdb;
    return $wpdb->get_results( "SELECT * FROM " . YPF_ADDONS_TABLE_NAME );
}

function ypf_display_addons_after_billing_form() {
    // Get the enabled setting from options
    $is_enabled = get_option('ypf_addons_checkout_enabled');

    // If the option is not '1', return early
    if ($is_enabled !== '1') {
        return;
    }

    // Unset the session variables related to the add-ons when the page loads
    if (function_exists('WC') && isset(WC()->session)) {
        WC()->session->__unset('chosen_addon');
        WC()->session->__unset('chosen_addon_percentage');
    }

    // Assuming you have a function to get your add-ons
    $addons_title = get_option('ypf_addons_checkout_title'); // Get the title set in the plugin settings
    $addons = get_addons_data_default(); // Replace with your actual function to get add-ons

    // Get the current products in the cart
    $cart = WC()->cart->get_cart();
    $exclude_addons = false;

    foreach ($cart as $cart_item) {
        $product_id = $cart_item['product_id'];
        $exclude_from_ypf_addon = get_post_meta($product_id, '_exclude_from_ypf_addon', true);

        if ($exclude_from_ypf_addon === 'yes') {
            $exclude_addons = true;
            break;
        }
    }

    // If any product is excluded from add-ons, return early
    if ($exclude_addons) {
        return;
    }

    if (!empty($addons)) {
        // CSS for styling the container and labels
       echo '<style>
                .ypf-addons-default-container {
                    margin-top: 20px;
                }
                .ypf-addons-default-container > div.ypf-addons-wrap{
                    display: flex;
                    flex-wrap: wrap;
                    gap: 10px;
                }
                .ypf-addons-default-container label {
                    flex: 1;
                    display: flex;
                    align-items: center;
                    background: transparent;
                    border: 1px solid #ccc;
                    color:#000000;
                    padding: 10px;
                    border-radius: 4px;
                    margin-bottom: 0; /* Adjust this as needed */
                }
                .ypf-addons-default-container input[type="radio"] {
                    margin-right: 8px; /* Adjust spacing to the right of the radio button */
                }
                /* Adjustments for small screens */
                @media (max-width: 768px) {
                    .ypf-addons-default-container label {
                        flex-basis: 100%;
                    }
                }
            </style>';

        echo '<div class="ypf-addons-default-container">';
        echo '<h4 class="heading ypf-addons-default-title">' . esc_html($addons_title) . '</h4>'; // Output the retrieved title
        echo '<div class="ypf-addons-wrap">';

        $chosen_addon_id = null;
        if (function_exists('WC') && isset(WC()->session)) {
            $chosen_addon_id = WC()->session->get('chosen_addon');
        }

        $isFirst = true; // Untuk menandai item pertama
        foreach ($addons as $addon) {
            $is_checked = ($addon->id == $chosen_addon_id || $isFirst) ? 'checked' : '';
            echo '<label>';
            echo '<input type="radio" class="ypf-addons-default-radio-input" name="ypf_addon" value="' . esc_attr($addon->id) . '" data-value="' . $addon->value_percentage . '" ' . $is_checked . '>';
            echo esc_html($addon->addon_name);
            $display_percentage = (intval($addon->value_percentage) == floatval($addon->value_percentage)) ? intval($addon->value_percentage) : floatval($addon->value_percentage);
            echo ' (+' . esc_html($display_percentage) . '%)';
            echo '</label>';
            $isFirst = false; // Setelah item pertama, setel ini ke false
        }

        echo '</div>';
        echo '</div>';
    }
}
add_action('woocommerce_after_checkout_billing_form', 'ypf_display_addons_after_billing_form');

// Hook to save the chosen add-on to order meta
add_action('woocommerce_checkout_create_order', 'save_chosen_addon_to_order_meta', 10, 2);
function save_chosen_addon_to_order_meta($order, $data) {
    // Get the enabled setting from options
    $is_enabled = get_option('ypf_addons_checkout_enabled');

    // If the option is not '1', return early
    if ($is_enabled !== '1') {
        return;
    }
    
    if (isset($_POST['ypf_addon']) && !empty($_POST['ypf_addon'])) {
        $order->update_meta_data('ypf_chosen_addon', sanitize_text_field($_POST['ypf_addon']));
    }
}

// Display the chosen add-on in the order admin panel
add_action('woocommerce_admin_order_data_after_billing_address', 'display_chosen_addon_in_admin_order_meta', 10, 1);
function display_chosen_addon_in_admin_order_meta($order) {
    $multi_currency_enabled = get_option('fyfx_your_propfirm_plugin_enable_multi_currency');
    if ($multi_currency_enabled === 'enable') {  
        $chosen_addon_id = $order->get_meta('ypf_chosen_addon');
        if ($chosen_addon_id) {
            echo '<p><strong>' . __('Chosen Add-on') . ':</strong> ' . esc_html($chosen_addon_id) . '</p>';
        }
    }
}

add_action('woocommerce_product_options_pricing', 'add_exclude_from_ypf_addon_checkbox');
function add_exclude_from_ypf_addon_checkbox() {
    woocommerce_wp_checkbox(
        array(
            'id'            => '_exclude_from_ypf_addon',
            'wrapper_class' => '',
            'label'         => __('Exclude from YPF Add-on', 'woocommerce'),
            'description'   => __('Check this box to exclude this product from YPF add-ons.', 'woocommerce')
        )
    );
}

add_action('woocommerce_process_product_meta', 'save_exclude_from_ypf_addon_checkbox');
function save_exclude_from_ypf_addon_checkbox($post_id) {
    $exclude_from_ypf_addon = isset($_POST['_exclude_from_ypf_addon']) ? 'yes' : 'no';
    update_post_meta($post_id, '_exclude_from_ypf_addon', $exclude_from_ypf_addon);
}