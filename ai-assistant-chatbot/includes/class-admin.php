<?php
if (!defined('ABSPATH')) exit;

// Add admin menu
add_action('admin_menu', function () {
    add_menu_page(
        __('AI Assistant ChatBot', 'ai-assistant-chatbot-full-control'),
        __('AI ChatBot', 'ai-assistant-chatbot-full-control'),
        'manage_options',
        'ai-assistant-chatbot',
        'ai_assistant_chatbot_settings_page',
        'dashicons-format-chat',
        81
    );
});

// Register settings
add_action('admin_init', function () {
    register_setting('ai_assistant_chatbot_group', 'ai_assistant_chatbot_options', [
        'type' => 'array',
        'sanitize_callback' => 'ai_assistant_chatbot_sanitize',
        'default' => [
            'bot_name'        => 'AI Assistant',
            'api_url'         => 'https://example.com/api',
            'primary_color'   => '#0078d7',
            'secondary_color' => '#f4f6f8',
            'bot_message_bg'  => '#e5e5e5',
            'user_icon'       => '🧑',
            'bot_icon'        => '🤖',
            'quick_buttons'   => 'Pricing,Payment Methods,Technical Support,Services,Free Services,Subscriptions,Game Top-ups,Special Offers,Delivery Info,Contact Us',
            'warning_message' => 'Automated responses may sometimes be incorrect.',
            'send_button_text'=> 'Send',
            'enable_sound'    => 1,
            'sound_url'       => 'https://actions.google.com/sounds/v1/cartoon/wood_plank_flicks.ogg'
        ]
    ]);
});

// Sanitize callback
function ai_assistant_chatbot_sanitize($input) {
    $out = [];

    $out['bot_name'] = isset($input['bot_name']) ? sanitize_text_field($input['bot_name']) : 'AI Assistant';
    $out['api_url']  = isset($input['api_url']) ? esc_url_raw($input['api_url']) : 'https://example.com/api';
    $out['primary_color'] = isset($input['primary_color']) ? sanitize_text_field($input['primary_color']) : '#0078d7';
    $out['secondary_color'] = isset($input['secondary_color']) ? sanitize_text_field($input['secondary_color']) : '#f4f6f8';
    $out['bot_message_bg'] = isset($input['bot_message_bg']) ? sanitize_text_field($input['bot_message_bg']) : '#e5e5e5';
    $out['user_icon'] = isset($input['user_icon']) ? wp_strip_all_tags($input['user_icon']) : '🧑';
    $out['bot_icon']  = isset($input['bot_icon']) ? wp_strip_all_tags($input['bot_icon']) : '🤖';
    $out['quick_buttons'] = isset($input['quick_buttons']) ? wp_strip_all_tags($input['quick_buttons']) : 'Pricing,Payment Methods,Technical Support,Services';
    $out['warning_message'] = isset($input['warning_message']) ? sanitize_textarea_field($input['warning_message']) : 'Automated responses may sometimes be incorrect.';
    $out['send_button_text'] = isset($input['send_button_text']) ? sanitize_text_field($input['send_button_text']) : 'Send';
    $out['enable_sound'] = isset($input['enable_sound']) ? intval($input['enable_sound']) : 1;
    $out['sound_url'] = isset($input['sound_url']) ? esc_url_raw($input['sound_url']) : 'https://actions.google.com/sounds/v1/cartoon/wood_plank_flicks.ogg';

    return $out;
}

// Admin page HTML
function ai_assistant_chatbot_settings_page() {
    if (!current_user_can('manage_options')) return;
    $opts = get_option('ai_assistant_chatbot_options', []);
    $defaults = [
        'bot_name' => 'AI Assistant',
        'api_url'  => 'https://example.com/api',
        'primary_color' => '#0078d7',
        'secondary_color' => '#f4f6f8',
        'bot_message_bg' => '#e5e5e5',
        'user_icon' => '🧑',
        'bot_icon'  => '🤖',
        'quick_buttons' => 'Pricing,Payment Methods,Technical Support,Services,Free Services,Subscriptions,Game Top-ups,Special Offers,Delivery Info,Contact Us',
        'warning_message' => 'Automated responses may sometimes be incorrect.',
        'send_button_text'=> 'Send',
        'enable_sound'    => 1,
        'sound_url'       => 'https://actions.google.com/sounds/v1/cartoon/wood_plank_flicks.ogg'
    ];
    $opts = wp_parse_args($opts, $defaults);
    ?>
    <div class="wrap">
        <h1><?php _e('AI Assistant ChatBot — Full Control Settings', 'ai-assistant-chatbot-full-control'); ?></h1>
        <p><?php _e('All options can be edited here. Changes reflect immediately on the frontend.', 'ai-assistant-chatbot-full-control'); ?></p>

        <form method="post" action="options.php">
            <?php settings_fields('ai_assistant_chatbot_group'); ?>

            <h2><?php _e('General', 'ai-assistant-chatbot-full-control'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="bot_name"><?php _e('Bot Name', 'ai-assistant-chatbot-full-control'); ?></label></th>
                    <td><input id="bot_name" name="ai_assistant_chatbot_options[bot_name]" type="text" value="<?php echo esc_attr($opts['bot_name']); ?>"></td>
                </tr>
                <tr>
                    <th><label for="api_url"><?php _e('API URL', 'ai-assistant-chatbot-full-control'); ?></label></th>
                    <td><input id="api_url" name="ai_assistant_chatbot_options[api_url]" type="url" value="<?php echo esc_attr($opts['api_url']); ?>"></td>
                </tr>
                <tr>
                    <th><label for="warning_message"><?php _e('Warning Message (English)', 'ai-assistant-chatbot-full-control'); ?></label></th>
                    <td><textarea id="warning_message" name="ai_assistant_chatbot_options[warning_message]" rows="3"><?php echo esc_textarea($opts['warning_message']); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="send_button_text"><?php _e('Send Button Text', 'ai-assistant-chatbot-full-control'); ?></label></th>
                    <td><input id="send_button_text" name="ai_assistant_chatbot_options[send_button_text]" type="text" value="<?php echo esc_attr($opts['send_button_text']); ?>"></td>
                </tr>
            </table>

            <h2><?php _e('Sound Settings', 'ai-assistant-chatbot-full-control'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><?php _e('Enable Sound', 'ai-assistant-chatbot-full-control'); ?></th>
                    <td>
                        <input type="checkbox" name="ai_assistant_chatbot_options[enable_sound]" value="1" <?php checked( isset($opts['enable_sound']) ? $opts['enable_sound'] : 1, 1 ); ?> />
                        <span><?php _e('Check to enable new message sound', 'ai-assistant-chatbot-full-control'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Sound URL', 'ai-assistant-chatbot-full-control'); ?></th>
                    <td>
                        <input type="url" name="ai_assistant_chatbot_options[sound_url]" value="<?php echo esc_attr( isset($opts['sound_url']) ? $opts['sound_url'] : 'https://actions.google.com/sounds/v1/cartoon/wood_plank_flicks.ogg'); ?>" size="50" />
                        <p class="description"><?php _e('Enter full URL to the notification sound.', 'ai-assistant-chatbot-full-control'); ?></p>
                    </td>
                </tr>
            </table>

            <h2><?php _e('Appearance', 'ai-assistant-chatbot-full-control'); ?></h2>
            <table class="form-table">
                <tr><th><?php _e('Primary Color', 'ai-assistant-chatbot-full-control'); ?></th><td><input type="color" name="ai_assistant_chatbot_options[primary_color]" value="<?php echo esc_attr($opts['primary_color']); ?>"></td></tr>
                <tr><th><?php _e('Secondary Color', 'ai-assistant-chatbot-full-control'); ?></th><td><input type="color" name="ai_assistant_chatbot_options[secondary_color]" value="<?php echo esc_attr($opts['secondary_color']); ?>"></td></tr>
                <tr><th><?php _e('Bot Message Background', 'ai-assistant-chatbot-full-control'); ?></th><td><input type="color" name="ai_assistant_chatbot_options[bot_message_bg]" value="<?php echo esc_attr($opts['bot_message_bg']); ?>"></td></tr>
                <tr><th><?php _e('User Icon', 'ai-assistant-chatbot-full-control'); ?></th><td><input type="text" name="ai_assistant_chatbot_options[user_icon]" value="<?php echo esc_attr($opts['user_icon']); ?>"></td></tr>
                <tr><th><?php _e('Bot Icon', 'ai-assistant-chatbot-full-control'); ?></th><td><input type="text" name="ai_assistant_chatbot_options[bot_icon]" value="<?php echo esc_attr($opts['bot_icon']); ?>"></td></tr>
            </table>

            <h2><?php _e('Quick Replies', 'ai-assistant-chatbot-full-control'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><?php _e('Quick Buttons (comma-separated)', 'ai-assistant-chatbot-full-control'); ?></th>
                    <td><textarea name="ai_assistant_chatbot_options[quick_buttons]" rows="3"><?php echo esc_textarea($opts['quick_buttons']); ?></textarea></td>
                </tr>
            </table>

            <?php submit_button( __('Save Changes', 'ai-assistant-chatbot-full-control') ); ?>
        </form>
    </div>
    <?php
}
