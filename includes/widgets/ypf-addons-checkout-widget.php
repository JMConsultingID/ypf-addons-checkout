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
            $chosen_addon_id = WC()->session->get('chosen_addon');
            foreach ( $addons as $addon ) {
                $is_checked = ($addon->id == $chosen_addon_id) ? 'checked' : '';
                echo '<label>';
                echo '<input type="radio" name="ypf_addon" value="' . esc_attr( $addon->id ) . '" data-value="' . $addon->value_percentage. '"' . $is_checked . '>';
                echo esc_html( $addon->addon_name );
                $display_percentage = (intval($addon->value_percentage) == floatval($addon->value_percentage)) ? intval($addon->value_percentage) : floatval($addon->value_percentage);
                echo ' (+' . esc_html($display_percentage) . '%)';
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