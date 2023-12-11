<?php

class YPF_Addons_Checkout_Settings {

    public function __construct() {
        add_action('admin_init', array($this, 'init_settings'));
    }

    public function init_settings() {
        register_setting('ypf_addons_options_group', 'ypf_addons_enabled');

        add_settings_section(
            'ypf_addons_settings_section', 
            'General Settings', 
            null, 
            'ypf-addons-checkout'
        );

        add_settings_field(
            'ypf_addons_enabled', 
            'Enable YPF Addons', 
            array($this, 'ypf_addons_enabled_callback'), 
            'ypf-addons-checkout', 
            'ypf_addons_settings_section'
        );
    }

    public function ypf_addons_enabled_callback() {
        $option = get_option('ypf_addons_enabled');
        echo '<input type="checkbox" id="ypf_addons_enabled" name="ypf_addons_enabled" value="1" ' . checked(1, $option, false) . '>';
    }

    public function options_page() {
        ?>
        <div class="wrap">
            <form method="post" action="options.php">
                <?php
                settings_fields('ypf_addons_options_group');
                do_settings_sections('ypf-addons-checkout');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
