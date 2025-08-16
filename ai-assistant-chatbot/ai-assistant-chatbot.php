<?php
/*
Plugin Name: AI Assistant ChatBot Full Control
Plugin URI: https://amarire.dev/wp-plugins
Description: Fully featured chat widget plugin with all defaults, editable from WP Admin for beginners.
Version: 1.1.0
Author: Amarire Dev
Author URI: https://amarire.dev
License: GPL2
Text Domain: ai-assistant-chatbot-full-control
*/

if (!defined('ABSPATH')) exit;

/**
 * Basic Settings
 */
define('AI_CHATBOT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AI_CHATBOT_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Include Required Files
 */
require_once AI_CHATBOT_PLUGIN_DIR . 'includes/class-admin.php';
require_once AI_CHATBOT_PLUGIN_DIR . 'includes/class-frontend.php';

/**
 * Ensure jQuery is Loaded
 */
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('jquery');
});
