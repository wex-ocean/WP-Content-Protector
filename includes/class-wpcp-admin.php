<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( is_admin() ) {
    new WPCP_Admin();
}

class WPCP_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function add_settings_page() {
        add_options_page(
            'Content Protector',
            'Content Protector',
            'manage_options',
            'wp-content-protector',
            array( $this, 'render_settings_page' )
        );
    }

    public function register_settings() {
        register_setting( 'wpcp_settings_group', 'wpcp_options' );
    }

    public function render_settings_page() {
        echo '<div class="wrap"><h1>WP Content Protector</h1><p>Settings coming soon.</p></div>';
    }
}