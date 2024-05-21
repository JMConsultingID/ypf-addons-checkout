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

// Add a custom field to WooCommerce product
function your_propfirm_addon_add_program_id_unlimited_field() {
    global $woocommerce, $post;

    // Get the product ID
    $product_id = $post->ID;

    // Display the custom field on the product edit page
    woocommerce_wp_text_input(
        array(
            'id'          => '_program_id_unlimited',
            'label'       => __('Program Id Unlimited(Your Propfirm)', 'woocommerce'),
            'placeholder' => __('Enter Program Id Unlimited(Your Propfirm)', 'woocommerce'),
            'desc_tip'    => true,
            'description' => __('Enter Program Id Unlimited(Your Propfirm).', 'woocommerce'),
            'wrapper_class' => 'show_if_simple',
        )
    );
}
add_action('woocommerce_product_options_general_product_data', 'your_propfirm_addon_add_program_id_unlimited_field', 9);

// Save the custom field value
function your_propfirm_addon_save_program_id_unlimited_field($product_id) {
    $program_id = sanitize_text_field($_POST['_program_id_unlimited']);
    update_post_meta($product_id, '_program_id_unlimited', esc_attr($program_id));
}
add_action('woocommerce_process_product_meta', 'your_propfirm_addon_save_program_id_unlimited_field');

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
        'woocommerce',
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

function ypf_addons_list_page(){
    global $wpdb;

    $edit = false;
    $addon_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $addon_name = '';
    $value_percentage = '';

    // Check if in edit mode
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && $addon_id > 0) {
        $edit = true;
        $addon = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . YPF_ADDONS_TABLE_NAME . " WHERE id = %d", $addon_id ) );
        if ($addon) {
            $addon_name = $addon->addon_name;
            $value_percentage = $addon->value_percentage;
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

        if ($edit) {
            // Update existing add-on
            $wpdb->update(
                YPF_ADDONS_TABLE_NAME,
                array('addon_name' => $submitted_addon_name, 'value_percentage' => $submitted_value_percentage),
                array('id' => $addon_id)
            );
        } else {
            // Insert new add-on
            $wpdb->insert(
                YPF_ADDONS_TABLE_NAME,
                array('addon_name' => $submitted_addon_name, 'value_percentage' => $submitted_value_percentage)
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
                    <th scope="row" style="border: 1px solid #ddd; padding: 8px;">Value (Percentage):</th>
                    <th scope="row" style="border: 1px solid #ddd; padding: 8px;"></th> <!-- Empty for the button -->
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">
                        <input type="text" name="addon_name" value="<?php echo esc_attr($addon_name); ?>" style="width: 100%;" />
                    </td>
                    <td style="border: 1px solid #ddd; padding: 8px;">
                        <input type="text" name="value_percentage" value="<?php echo esc_attr($value_percentage); ?>" style="width: 100%;" />
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
                    <th scope="col">Value (Percentage)</th>
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
                    background: #f7f7f7;
                    border: 1px solid #ccc;
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
    if (isset($_POST['ypf_addon']) && !empty($_POST['ypf_addon'])) {
        $order->update_meta_data('ypf_chosen_addon', sanitize_text_field($_POST['ypf_addon']));
    }
}

// Display the chosen add-on in the order admin panel
add_action('woocommerce_admin_order_data_after_billing_address', 'display_chosen_addon_in_admin_order_meta', 10, 1);
function display_chosen_addon_in_admin_order_meta($order) {
    $chosen_addon_id = $order->get_meta('ypf_chosen_addon');
    if ($chosen_addon_id) {
        echo '<p><strong>' . __('Chosen Add-on') . ':</strong> ' . esc_html($chosen_addon_id) . '</p>';
    }
}


// add_action( 'woocommerce_checkout_create_order', 'custom_update_order_meta_based_on_addon', 10, 2 );

// function custom_update_order_meta_based_on_addon( $order, $data ) {
//     $items = $order->get_items();
//     $addon_product_id = 342;
//     $has_addon = false;

//     foreach ( $items as $item ) {
//         if ( $item->get_product_id() == $addon_product_id ) {
//             $has_addon = true;
//             break;
//         }
//     }

//     foreach ( $items as $item ) {
//         $product_id = $item->get_product_id();
//         $product = wc_get_product( $product_id );

//         if ( $product_id != $addon_product_id ) {
//             $program_id_key = $has_addon ? '_program_id_unlimited' : '_program_id';
//             $program_id = $product->get_meta( $program_id_key );
//             $order->update_meta_data( 'prog_id', $program_id );
//         }
//     }
// }
