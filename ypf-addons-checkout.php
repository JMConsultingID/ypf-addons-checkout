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

// Include required files
require_once plugin_dir_path( __FILE__ ) . 'includes/class-ypf-addons-checkout.php';

// Register plugin settings page
add_action( 'admin_menu', 'ypf_addons_checkout_settings_page' );

function ypf_addons_checkout_settings_page() {
  add_menu_page(
    'YPF Addons Settings',
    'YPF Addons',
    'manage_options',
    'ypf_addons_checkout_settings',
    'ypf_addons_checkout_settings_page',
    'dashicons-cart',
    56
  );
  
  // Add submenu for add-ons list
  add_submenu_page(
    'ypf_addons_checkout_settings',
    'Add-Ons List',
    'Add-Ons List',
    'manage_options',
    'ypf_addons_checkout_add_ons_list',
    'ypf_addons_checkout_add_ons_list_page'
  );
}

// Render plugin settings page
function ypf_addons_checkout_settings_page() {
  ?>
  <div class="wrap">
    <h1>YPF Addons Checkout Settings</h1>
    <form method="post" action="options.php">
      <?php settings_fields( 'ypf_addons_checkout_settings_group' ); ?>
      <?php do_settings_sections( 'ypf_addons_checkout_settings_page' ); ?>
      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row">Enable Add-Ons</th>
            <td>
              <input type="checkbox" name="ypf_addons_checkout_enable_addons" value="1" <?php checked( get_option( 'ypf_addons_checkout_enable_addons', 1 ), 1 ); ?>>
              <label for="ypf_addons_checkout_enable_addons">Enable add-ons functionality.</label>
            </td>
          </tr>
        </tbody>
      </table>
      <?php submit_button(); ?>
    </form>
  </div>
  <?php
}

// Register settings
add_action( 'admin_init', 'ypf_addons_checkout_register_settings' );

function ypf_addons_checkout_register_settings() {
  register_setting( 'ypf_addons_checkout_settings_group', 'ypf_addons_checkout_enable_addons' );
}
