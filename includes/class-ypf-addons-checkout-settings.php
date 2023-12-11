<?php

class YPF_Addons_Checkout_Settings {

    public function __construct() {
        // Constructor code here.
    }

    public function options_page() {
        ?>
        <div class="wrap">
            <h1>YPF Addons Settings</h1>
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

    // Other methods for settings.
}
