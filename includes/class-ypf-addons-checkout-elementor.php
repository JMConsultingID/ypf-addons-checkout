<?php
// class-ypf-addons-checkout-elementor.php

class YPF_Addons_Checkout_Elementor {

    public function __construct() {
        // Hook into Elementor widgets registered.
        add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );
        add_action( 'elementor/elements/categories_registered', [ $this, 'add_elementor_widget_categories' ] );
    }

    public function add_elementor_widget_categories( $elements_manager ) {
        $elements_manager->add_category(
            'ypf-addons-category',
            [
                'title' => __( 'YPF Addons', 'ypf-addons-checkout' ),
                'icon' => 'fa fa-plug',
            ]
        );
    }

    public function register_widgets() {
        // Its is now safe to include Widget files
        require_once( 'ypf-addons-checkout-widget.php' );
        
        // Register the widget
        \Elementor\Plugin::instance()->widgets_manager->register( new \YPF_Addons_Checkout_Widget() );
    }
}

// Instantiate the class
new YPF_Addons_Checkout_Elementor();
