class AI_Chatbot_Frontend {

    public function __construct() {
        add_action('wp_footer', [$this, 'load_chat_widget']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets() {
        wp_enqueue_style('ai-chatbot-css', AI_CHATBOT_PLUGIN_URL . 'assets/css/chat.css');
        wp_enqueue_script('ai-chatbot-js', AI_CHATBOT_PLUGIN_URL . 'assets/js/chat.js', ['jquery'], null, true);
    }

    public function load_chat_widget() {
        include AI_CHATBOT_PLUGIN_DIR . 'templates/chat-widget.php';
    }
}

// Initialize Frontend
new AI_Chatbot_Frontend();
