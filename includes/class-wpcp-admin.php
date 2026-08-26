<?php
if (!defined('ABSPATH')) {
    exit;
}

class WPCP_Content_Protector_Admin
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_settings_page()
    {
        add_options_page(
            'Content Protector',
            'Content Protector',
            'manage_options',
            'wp-content-protector',
            array($this, 'render_settings_page')
        );
    }

    // public function register_settings() {
    //     register_setting( 'wpcp_settings_group', 'wpcp_options' );
    // }

    // public function render_settings_page() {
    //     echo '<div class="wrap"><h1>WP Content Protector</h1><p>Settings coming soon.</p></div>';
    // }

    public function register_settings()
    {
        register_setting('wpcp_settings_group', 'wpcp_options', array($this, 'sanitize_options'));
    }

    private function get_labels()
    {
        return array(
            'disable_right_click' => 'Disable right-click',
            'disable_text_select' => 'Prevent text selection',
            'disable_copy' => 'Block copy (Ctrl+C)',
            'disable_cut' => 'Block cut (Ctrl+X)',
            'disable_drag_images' => 'Prevent image dragging',
            'disable_view_source' => 'Block view-source (Ctrl+U)',
            'disable_devtools_f12' => 'Block F12',
            'disable_devtools_ctrl_shift_i' => 'Block Ctrl+Shift+I',
            'disable_console_ctrl_shift_j' => 'Block Ctrl+Shift+J',
        );
    }

    public function sanitize_options($input)
    {
        if (!current_user_can('manage_options')) {
            return wpcp_content_protector_get_options();
        }

        $input = is_array($input) ? $input : array();
        $output = array();
        foreach (array_keys($this->get_labels()) as $key) {
            $output['protections'][$key] = !empty($input['protections'][$key]) ? 1 : 0;
        }
        $output['excluded_post_types'] = isset($input['excluded_post_types']) ? array_map('sanitize_key', (array) $input['excluded_post_types']) : array();
        $post_ids = isset($input['excluded_post_ids']) ? explode(',', sanitize_text_field($input['excluded_post_ids'])) : array();
        $output['excluded_post_ids'] = implode(',', array_filter(array_map('absint', $post_ids)));
        $valid_roles = array_keys(wp_roles()->get_names());
        $roles = isset($input['excluded_roles']) ? array_map('sanitize_key', (array) $input['excluded_roles']) : array();
        $output['excluded_roles'] = array_values(array_intersect($roles, $valid_roles));
        $output['show_message'] = !empty($input['show_message']) ? 1 : 0;
        $output['custom_message'] = isset($input['custom_message']) ? sanitize_text_field($input['custom_message']) : '';
        return $output;
    }

    public function render_settings_page()
    {
        if (!current_user_can('manage_options'))
            return;
        $options = wpcp_content_protector_get_options();
        ?>
            <div class="wrap">
                <h1>WP Content Protector</h1>
                <form method="post" action="options.php">
                    <?php settings_fields('wpcp_settings_group'); ?>
                    <h2>Protections</h2>
                    <?php foreach ($this->get_labels() as $key => $label): ?>
                            <label style="display:block;margin-bottom:6px;">
                                <input type="checkbox" name="wpcp_options[protections][<?php echo esc_attr($key); ?>]" value="1" <?php checked(!empty($options['protections'][$key])); ?> />
                                <?php echo esc_html($label); ?>
                            </label>
                    <?php endforeach; ?>

                    <h2>Exclusions</h2>
                    <p>Exclude post/page IDs (comma-separated):<br>
                    <input type="text" name="wpcp_options[excluded_post_ids]" value="<?php echo esc_attr($options['excluded_post_ids']); ?>" class="regular-text" /></p>

                    <p>Exclude post types:<br>
                    <?php foreach (get_post_types(array('public' => true), 'objects') as $post_type): ?>
                            <label style="display:inline-block;margin-right:12px;">
                                <input type="checkbox" name="wpcp_options[excluded_post_types][]" value="<?php echo esc_attr($post_type->name); ?>" <?php checked(in_array($post_type->name, (array) $options['excluded_post_types'], true)); ?> />
                                <?php echo esc_html($post_type->labels->name); ?>
                            </label>
                    <?php endforeach; ?></p>

                    <p>Exclude roles:<br>
                    <?php foreach (wp_roles()->get_names() as $role_key => $role_label): ?>
                            <label style="display:inline-block;margin-right:12px;">
                                <input type="checkbox" name="wpcp_options[excluded_roles][]" value="<?php echo esc_attr($role_key); ?>" <?php checked(in_array($role_key, (array) $options['excluded_roles'], true)); ?> />
                                <?php echo esc_html($role_label); ?>
                            </label>
                    <?php endforeach; ?></p>

                    <h2>Message</h2>
                    <label><input type="checkbox" name="wpcp_options[show_message]" value="1" <?php checked(!empty($options['show_message'])); ?> /> Show message on blocked action</label><br><br>
                    <input type="text" name="wpcp_options[custom_message]" value="<?php echo esc_attr($options['custom_message']); ?>" class="regular-text" />

                    <?php submit_button(); ?>
                </form>
            </div>
            <?php
    }
}