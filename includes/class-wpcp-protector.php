<?php
if (!defined('ABSPATH')) {
    exit;
}

class WPCP_Protector
{

    private $options;

    public function __construct()
    {
        $this->options = wpcp_get_options();
        add_action('wp_enqueue_scripts', array($this, 'maybe_enqueue_assets'));
    }

    private function should_protect()
    {
        if (is_admin() || is_feed()) {
            return false;
        }

        if (empty(array_filter($this->options['protections']))) {
            return false;
        }

        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            if (array_intersect((array) $this->options['excluded_roles'], (array) $user->roles)) {
                return false;
            }
        }

        $post_type = get_post_type();
        if ($post_type && in_array($post_type, (array) $this->options['excluded_post_types'], true)) {
            return false;
        }

        $excluded_ids = array_filter(array_map('trim', explode(',', $this->options['excluded_post_ids'])));
        if (is_singular() && in_array((string) get_the_ID(), $excluded_ids, true)) {
            return false;
        }

        return true;
    }
    public function maybe_enqueue_assets()
    {
        if (!$this->should_protect()) {
            return;
        }

        wp_enqueue_style('wpcp-protector', WPCP_URL . 'assets/css/wpcp-protector.css', array(), WPCP_VERSION);
        wp_enqueue_script('wpcp-protector', WPCP_URL . 'assets/js/wpcp-protector.js', array(), WPCP_VERSION, true);
        wp_localize_script('wpcp-protector', 'wpcpSettings', array(
            'protections' => $this->options['protections'],
        ));
    }

}
