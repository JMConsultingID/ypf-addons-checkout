<?php
// ypf-addons-checkout-widget.php

class YPF_Addons_Checkout_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'ypf_addons_checkout';
    }

    public function get_title() {
        return __( 'YPF Addons Checkout', 'ypf-addons-checkout' );
    }

    public function get_icon() {
        return 'fa fa-code';
    }

    public function get_categories() {
        return [ 'ypf-addons-category' ];
    }

    protected function _register_controls() {
        // Register widget controls here
    }

    protected function render() {
        $addons = $this->get_addons_data();
        if ( ! empty( $addons ) ) {
            echo '<form>';
            foreach ( $addons as $addon ) {
                // Assume each addon has 'id', 'addon_name', and 'value_percentage' properties
                echo '<label>';
                echo '<input type="radio" name="ypf_addon" value="' . esc_attr( $addon->id ) . '"> ';
                echo esc_html( $addon->addon_name );
                echo '</label><br>';
            }
            echo '</form>';
        }
    }

    private function get_addons_data() {
        global $wpdb;
        return $wpdb->get_results( "SELECT * FROM " . YPF_ADDONS_TABLE_NAME );
    }
}