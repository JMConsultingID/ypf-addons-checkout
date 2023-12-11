<?php
if ( ! class_exists( 'YPF_Addons_Checkout_Settings' ) ) {
    class YPF_Addons_Checkout_Settings {
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
                <?php
                // Show the "Add New Add-on" form
                ypf_addons_checkout_addon_form();

                // Settings form
                ?>
                <form method="post" action="options.php">
                    <?php
                    settings_fields('ypf-addons-checkout-options');
                    do_settings_sections('ypf-addons-checkout');
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
}

// Function to create the database table for addon fees
function ypf_addons_checkout_create_db_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ypf_addons_checkout';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        addon_name varchar(255) NOT NULL,
        percentage decimal(5,2) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Function to handle the form submission for saving a new add-on
function ypf_addons_checkout_save_addon() {
    // Check for nonce for security
    check_admin_referer('ypf_addons_checkout_add');

    // Process and sanitize the input data
    $addon_name = sanitize_text_field($_POST['addon_name']);
    $percentage = sanitize_text_field($_POST['percentage']);

    // Insert the new add-on into the database
    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'ypf_addons_checkout',
        array(
            'addon_name' => $addon_name,
            'percentage' => $percentage,
        ),
        array(
            '%s',
            '%f',
        )
    );

    // Redirect back to the settings page with a success message
    wp_redirect(add_query_arg('ypf_addons_checkout_message', 'addon_added', menu_page_url('ypf-addons-checkout', false)));
    exit;
}
// Function to display the add-on form
function ypf_addons_checkout_addon_form() {
    ?>
    <h2>Add New Add-on</h2>
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="ypf_addons_checkout_save">
        <?php wp_nonce_field('ypf_addons_checkout_add'); ?>

        <table class="form-table">
            <tr>
                <th scope="row"><label for="addon_name">Add-on Name</label></th>
                <td><input name="addon_name" id="addon_name" type="text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="percentage">Value (Percentage)</label></th>
                <td><input name="percentage" id="percentage" type="text" required></td>
            </tr>
        </table>

        <?php submit_button('Add Add-on'); ?>
    </form>
    <?php
}