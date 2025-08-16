<?php
/*
Plugin Name: AI Assistant ChatBot Full Control
Plugin URI: https://amarire.dev/wp-plugins
Description: Fully featured chat widget plugin with all defaults, editable from WP Admin for beginners.
Version: 1.1.0
Author: Amarire Dev
Author URI: https://amarire.dev
License: GPL2
Text Domain: ai-assistant-chatbot
*/

if (!defined('ABSPATH')) exit;

// Basic Paths
define('AI_CHATBOT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AI_CHATBOT_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load Translations
load_plugin_textdomain('ai-assistant-chatbot', false, dirname(plugin_basename(__FILE__)) . '/languages');

// Include Admin and Frontend
require_once AI_CHATBOT_PLUGIN_DIR . 'includes/class-admin.php';
require_once AI_CHATBOT_PLUGIN_DIR . 'includes/class-frontend.php';

// Enqueue jQuery
function ai_chatbot_enqueue_jquery() {
    wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'ai_chatbot_enqueue_jquery');

// Initialize Admin & Frontend
if (is_admin()) {
    new AI_Chatbot_Admin();
}
new AI_Chatbot_Frontend();
