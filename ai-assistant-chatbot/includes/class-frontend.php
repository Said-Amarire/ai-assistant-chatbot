<?php
if (!defined('ABSPATH')) exit;

/**
 * Load chatbot interface in the footer
 */
add_action('wp_footer', function () {
    // Include the chat widget template
    include AI_CHATBOT_PLUGIN_DIR . 'templates/chat-widget.php';
});
