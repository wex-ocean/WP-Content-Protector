<?php
if (!defined('ABSPATH')) {
    exit;
}

class WPCP_Content_Protector
{

    private $options;
    private function should_protect()
    {
        if (is_admin() || is_feed()) {
            return false;
        }

        if (empty(array_filter((array) $this->options['protections']))) {
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

        $excluded_ids = array_filter(array_map('absint', explode(',', (string) $this->options['excluded_post_ids'])));
        if (is_singular() && in_array(absint(get_the_ID()), $excluded_ids, true)) {
            return false;
        }

        return true;
    }
    public function __construct()
    {
        $this->options = wpcp_content_protector_get_options();
        add_filter('body_class', array($this, 'maybe_add_body_class'));
        add_action('wp_enqueue_scripts', array($this, 'maybe_enqueue_assets'));
    }

    public function maybe_add_body_class($classes)
    {
        if ($this->should_protect() && !empty($this->options['protections']['disable_text_select'])) {
            $classes[] = 'wpcp-no-select';
        }
        return $classes;
    }

    public function maybe_enqueue_assets()
    {
        if (!$this->should_protect())
            return;

        wp_enqueue_style('wpcp-content-protector', WPCP_CONTENT_PROTECTOR_URL . 'assets/css/wpcp-protector.css', array(), WPCP_CONTENT_PROTECTOR_VERSION);
        wp_enqueue_script('wpcp-content-protector', WPCP_CONTENT_PROTECTOR_URL . 'assets/js/wpcp-protector.js', array(), WPCP_CONTENT_PROTECTOR_VERSION, true);
        wp_localize_script('wpcp-content-protector', 'wpcpContentProtectorSettings', array(
            'protections' => $this->options['protections'],
            'showMessage' => !empty($this->options['show_message']),
            'customMessage' => $this->options['custom_message'],
        ));
    }

}
