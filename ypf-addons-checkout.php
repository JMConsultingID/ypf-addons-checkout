<?php
/**
 * Plugin Name: YPF Addons Checkout
 * Description: WooCommerce custom checkout add-ons.
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('YPF_Addons_Checkout')) {

    class YPF_Addons_Checkout {

        private static $instance;

        private function __construct() {
            // Add hooks and filters here.
            add_action('admin_menu', array($this, 'add_admin_menu'));
            add_action('admin_init', array($this, 'settings'));
        }

        public static function get_instance() {
            if (null == self::$instance) {
                self::$instance = new self;
            }
            return self::$instance;
        }

        public function add_admin_menu() {
            add_menu_page(
                'YPF Addons Checkout',
                'YPF Addons Checkout',
                'manage_options',
                'ypf-addons-checkout',
                array($this, 'settings_page'),
                'dashicons-cart',
                26
            );
        }

        public function settings_page() {
            ?>
            <div class="wrap">
                <h2>YPF Addons Checkout Settings</h2>
                <form method="post" action="options.php">
                    <?php
                    settings_fields('ypf_addons_checkout_options');
                    do_settings_sections('ypf-addons-checkout');
                    submit_button();
                    ?>
                </form>
            </div>
            <?php
        }
    }

    public function settings() {
        register_setting('ypf_addons_checkout_options', 'ypf_addons_checkout_enable');

        add_settings_section(
            'ypf_addons_checkout_section',
            'General Settings',
            array($this, 'section_callback'),
            'ypf-addons-checkout'
        );

        add_settings_field(
            'ypf_addons_checkout_enable',
            'Enable YPF Addons Checkout',
            array($this, 'enable_callback'),
            'ypf-addons-checkout',
            'ypf_addons_checkout_section'
        );
    }

    public function section_callback() {
        echo 'Enable or disable YPF Addons Checkout';
    }

    public function enable_callback() {
        $enable = get_option('ypf_addons_checkout_enable');
        ?>
        <label for="ypf_addons_checkout_enable">
            <input type="checkbox" name="ypf_addons_checkout_enable" id="ypf_addons_checkout_enable" <?php checked(1, $enable, true); ?>>
            Enable YPF Addons Checkout
        </label>
        <?php
    }


    // Instantiate the class.
    YPF_Addons_Checkout::get_instance();
}
