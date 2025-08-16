<?php
if (!defined('ABSPATH')) exit;

/**
 * Load chatbot interface in the footer
 */
add_action('wp_footer', function () {
    include AI_CHATBOT_PLUGIN_DIR . 'templates/chat-widget.php';
});
