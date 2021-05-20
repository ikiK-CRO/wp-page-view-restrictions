<?php
/**
* Plugin Name: WP Page View Restrictions
* Plugin URI: https://wpcompress.appdiz-informatika.hr/
* Description: This is the very first plugin I ever created.
* Version: 1.0.0
* Author: Kristijan Bičak
* Author URI: www.appdiz-informatika.hr
**/


function wporg_options_page_html() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "wporg_options"
            settings_fields( 'wporg_options' );
            // output setting sections and their fields
            // (sections are registered for "wporg", each field is registered to a specific section)
            do_settings_sections( 'wporg' );
            // output save settings button
            submit_button( __( 'Save Settings', 'textdomain' ) );
            ?>
        </form>
    </div>
    <?php
}

function wporg_options_page()
{
    add_submenu_page(
        'options-general.php',
        'WP Page View Restrictions',
        'WP Page View Restrictions',
        'manage_options',
        'wporg',
        'wporg_options_page_html'
    );
}
add_action('admin_menu', 'wporg_options_page');