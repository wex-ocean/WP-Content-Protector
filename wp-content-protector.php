<?php
/**
 * Plugin Name: WP Content Protector
 * Description: Lightweight deterrent against content copying.
 * Version: 1.0.0
 * Author: OCEEAN
 */

if (!defined('ABSPATH')) {
    exit;
}
define('WPCP_CONTENT_PROTECTOR_VERSION', '1.0.0');
define('WPCP_CONTENT_PROTECTOR_PATH', plugin_dir_path(__FILE__));
define('WPCP_CONTENT_PROTECTOR_URL', plugin_dir_url(__FILE__));

require_once WPCP_CONTENT_PROTECTOR_PATH . 'includes/class-wpcp-admin.php';
require_once WPCP_CONTENT_PROTECTOR_PATH . 'includes/class-wpcp-protector.php';

if (is_admin()) {
    new WPCP_Content_Protector_Admin();
}

new WPCP_Content_Protector();

function wpcp_content_protector_default_options()
{
    return array(
        // 'protections' => array(
        //     'disable_right_click' => 1,
        //     'disable_text_select' => 1,
        //     'disable_copy' => 1,
        //     'disable_cut' => 1,
        //     'disable_drag_images' => 1,
        // ),
        'protections' => array(
            'disable_right_click' => 1,
            'disable_text_select' => 1,
            'disable_copy' => 1,
            'disable_cut' => 1,
            'disable_drag_images' => 1,
            'disable_view_source' => 0,
            'disable_devtools_f12' => 0,
            'disable_devtools_ctrl_shift_i' => 0,
            'disable_console_ctrl_shift_j' => 0,
        ),
        'excluded_post_types' => array(),
        'excluded_post_ids' => '',
        'excluded_roles' => array('administrator'),
        'show_message' => 1,
        'custom_message' => 'Content on this site is protected.',
    );
}

function wpcp_content_protector_activate()
{
    if (false === get_option('wpcp_options')) {
        add_option('wpcp_options', wpcp_content_protector_default_options());
    }
}
register_activation_hook(__FILE__, 'wpcp_content_protector_activate');

function wpcp_content_protector_get_options()
{
    $saved = get_option('wpcp_options', array());
    $defaults = wpcp_content_protector_default_options();
    $saved = is_array($saved) ? $saved : array();
    $saved['protections'] = isset($saved['protections']) && is_array($saved['protections']) ? $saved['protections'] : array();
    $saved['protections'] = wp_parse_args($saved['protections'], $defaults['protections']);
    return wp_parse_args($saved, $defaults);
}