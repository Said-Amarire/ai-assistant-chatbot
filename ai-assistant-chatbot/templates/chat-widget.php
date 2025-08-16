<?php
if (!defined('ABSPATH')) exit;

$opts = get_option('ai_assistant_chatbot_options', []);

// defaults
$defaults = [
    'bot_name' => 'AI Assistant',
    'api_url'  => 'https://example.com/api',
    'primary_color' => '#0078d7',
    'secondary_color' => '#f4f6f8',
    'bot_message_bg' => '#e5e5e5',
    'user_icon' => '🧑',
    'bot_icon'  => '🤖',
    'quick_buttons' => 'Pricing,Payment Methods,Technical Support,Services,Free Services,Subscriptions,Game Top-ups,Special Offers,Delivery Info,Contact Us',
    'warning_message' => __('Automated responses may sometimes be incorrect.', 'ai-assistant-chatbot-full-control'),
    'enable_sound' => 1,
    'sound_url' => 'https://actions.google.com/sounds/v1/cartoon/clang_and_wobble.ogg'
];

$opts = wp_parse_args($opts, $defaults);

$BOT_NAME = $opts['bot_name'];
$API_URL  = $opts['api_url'];
$PRIMARY_COLOR = $opts['primary_color'];
$SECONDARY_COLOR = $opts['secondary_color'];
$BOT_MSG_BG = $opts['bot_message_bg'];
$USER_ICON = $opts['user_icon'];
$BOT_ICON  = $opts['bot_icon'];
$QB_LIST = array_filter(array_map('trim', explode(',', $opts['quick_buttons'])));
$WARNING_MESSAGE = $opts['warning_message'];

$ENABLE_SOUND = isset($opts['enable_sound']) && $opts['enable_sound'] == 1 ? true : false;
$SOUND_URL = isset($opts['sound_url']) ? esc_url($opts['sound_url']) : 'https://actions.google.com/sounds/v1/cartoon/clang_and_wobble.ogg';
?>

<style>
/* ===== Chat Widget ===== */
#chat-widget { position: fixed; bottom: 25px; right: 25px; z-index: 9999; font-family: 'Segoe UI', sans-serif; }
#chat-toggle { background-color: <?php echo esc_attr($PRIMARY_COLOR); ?>; border: none; border-radius: 50%; width: 60px; height: 60px; cursor: pointer; display: flex; justify-content: center; align-items: center; box-shadow: 0 6px 12px rgba(0,0,0,0.25), inset 0 4px 6px rgba(0,0,0,0.12); transition: transform 0.25s ease; }
#chat-toggle:hover { transform: scale(1.08); box-shadow: 0 8px 16px rgba(0,0,0,0.28), inset 0 6px 8px rgba(0,0,0,0.14); }
#chat-toggle svg { width: 30px; height: 30px; fill: white; }

/* ===== Chat Box ===== */
#chat-box { display: none; flex-direction: column; width: 360px; max-height: 500px; background: #ffffff; border-radius: 20px; box-shadow: 0 12px 35px rgba(0,0,0,0.18); overflow: hidden; position: fixed; bottom: 80px; right: 25px; z-index: 9999; animation: slideIn 0.35s ease; }
@keyframes slideIn { 0% { opacity:0; transform:translateY(24px);} 100% {opacity:1; transform:translateY(0);} }
#chat-header { background-color: <?php echo esc_attr($PRIMARY_COLOR); ?>; color:white; padding:12px 15px; font-weight:600; font-size:16px; display:flex; justify-content:space-between; align-items:center; }
#chat-header span.close-chat { cursor:pointer; font-size:32px; font-weight:bold; line-height:1; transition:transform 0.2s; }
#chat-header span.close-chat:hover { transform: rotate(90deg); }
#chat-header span.clear-chat { cursor:pointer; font-size:22px; margin-left:10px; display:flex; align-items:center; }
#chat-header span.clear-chat svg { fill: #fff; width: 22px; height: 22px; }
#chat-header .title { font-weight:600; font-size:16px; }

/* Messages */
#messages { flex:1; padding:15px; overflow-y:auto; background: <?php echo esc_attr($SECONDARY_COLOR); ?>; display:flex; flex-direction:column; gap:10px; }
.message { max-width:75%; padding:10px 14px; border-radius:15px; line-height:1.4; font-size:14px; word-wrap:break-word; display:flex; flex-direction: column; gap:6px; }
.message.user { background: <?php echo esc_attr($PRIMARY_COLOR); ?>; color:white; align-self:flex-end; border-bottom-right-radius:0; }
.message.bot { background: <?php echo esc_attr($BOT_MSG_BG); ?>; color:#333; align-self:flex-start; border-bottom-left-radius:0; }
.message span.text { display: flex; align-items: flex-start; gap: 8px; word-break: break-word; }
.message span.time { display: block; font-size: 10px; margin-top: 4px; opacity: 0.8; }
.message.user span.time { color:#fff; align-self: flex-end; }
.message.bot span.time  { color:#000; align-self: flex-end; }

/* Typing Dots */
.typing .dots { display: flex; align-items: center; gap: 6px; margin-top: 6px; }
.typing .dots span { width: 6px; height: 6px; background-color: #555; border-radius: 50%; display: inline-block; animation: blink 1.2s infinite both; }
.typing .dots span:nth-child(1){ animation-delay: 0s; }
.typing .dots span:nth-child(2){ animation-delay: 0.15s; }
.typing .dots span:nth-child(3){ animation-delay: 0.3s; }
@keyframes blink { 0%,80%,100% { opacity:0; transform:scale(0.8); } 40% { opacity:1; transform:scale(1); } }

/* Input Area */
#chat-input-area { display:flex; border-top:1px solid #e0e0e0; padding:10px; background:#fff; align-items:center; }
#chat-input { flex:1; border:1px solid #ddd; border-radius:25px; padding:10px 14px; font-size:14px; }
#chat-input:focus { outline:none; border-color: <?php echo esc_attr($PRIMARY_COLOR); ?>; box-shadow:0 0 0 3px rgba(0,120,215,0.06); }
#send-btn { background: <?php echo esc_attr($PRIMARY_COLOR); ?>; border:none; padding:10px; margin-left:10px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.2s ease, transform 0.12s ease; box-shadow:inset 0 2px 4px rgba(0,0,0,0.08); }
#send-btn:hover { background:#005fa3; transform:translateY(-1px); }
#send-btn svg { width:18px; height:18px; fill:white; }

/* Quick Replies Scroll */
#quick-replies { display: flex; overflow-x: auto; gap: 8px; padding: 8px; background:#fafafa; border-top:1px solid #eee; height:50px; min-height:50px; flex-shrink:0; -webkit-overflow-scrolling: touch; }
#quick-replies::-webkit-scrollbar { display:none; }
.quick-btn { flex:0 0 auto; background: <?php echo esc_attr($PRIMARY_COLOR); ?>; color:white; border:none; border-radius:18px; padding:6px 12px; font-size:13px; cursor:pointer; transition:0.2s; white-space:nowrap; }
.quick-btn:hover { background:#005fa3; }

/* Warning message — visible */
#chat-warning { text-align:center; font-size:11px; color:#555; padding:8px 10px; background:#fafafa; border-top:1px solid #eee; display:block; line-height:1.3; }

/* Responsive */
@media (max-width:480px){
    #chat-box { width:95%; max-height:75vh; bottom:12px; right:50%; transform:translateX(50%); border-radius:14px; padding:0; }
    #chat-header span.close-chat { font-size:32px; }
    #chat-toggle { bottom:16px; right:4%; position:fixed; }
    #chat-input { font-size:13px; padding:9px 12px; }
    #send-btn { width:42px; height:42px; padding:8px; }
    .message { font-size:13px; padding:8px 12px; max-width:85%; }
}
body.chat-open { overflow:hidden !important; touch-action:none !important; position:fixed; width:100%; }
</style>

<div id="chat-widget">
    <button id="chat-toggle" aria-label="<?php echo esc_attr(__('Open chat', 'ai-assistant-chatbot-full-control')); ?>">
        <svg viewBox="0 0 24 24"><path d="M20 2H4C2.897 2 2 2.897 2 4v16l4-4h14c1.103 0 2-0.897 2-2V4C22 2.897 21.103 2 20 2z"/><circle cx="8" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="16" cy="12" r="1.5"/></svg>
    </button>
    <div id="chat-box">
        <div id="chat-header">
            <span class="title"><?php echo esc_html($BOT_NAME); ?></span>
            <span class="clear-chat" title="<?php echo esc_attr(__('Clear Chat', 'ai-assistant-chatbot-full-control')); ?>">
                <svg viewBox="0 0 24 24"><path d="M3 6h18v2H3V6zm2 3h14v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V9zm3 2v8h2v-8H8zm4 0v8h2v-8h-2zm4 0v8h2v-8h-2z"/></svg>
            </span>
            <span class="close-chat">×</span>
        </div>

        <div id="messages"></div>

        <div id="quick-replies">
            <?php foreach ($QB_LIST as $btn): ?>
                <button class="quick-btn"><?php echo esc_html($btn); ?></button>
            <?php endforeach; ?>
        </div>

        <div id="chat-input-area">
            <input type="text" id="chat-input" placeholder="<?php echo esc_attr(__('Type your message...', 'ai-assistant-chatbot-full-control')); ?>">
            <button id="send-btn">
                <svg viewBox="0 0 24 24"><path d="M2 21l21-9L2 3v7l15 2-15 2z"/></svg>
            </button>
        </div>

        <div id="chat-warning"><?php echo esc_html($WARNING_MESSAGE); ?></div>
    </div>
</div>

<audio id="new-message-sound" src="<?php echo esc_url($SOUND_URL); ?>"></audio>

<script>
(function(){
    const API_URL = <?php echo json_encode($API_URL); ?>;
    const BOT_ICON = <?php echo json_encode($BOT_ICON); ?>;
    const USER_ICON = <?php echo json_encode($USER_ICON); ?>;
    const ENABLE_SOUND = <?php echo $ENABLE_SOUND ? 'true' : 'false'; ?>;
    const sound = document.getElementById("new-message-sound");

    const toggleBtn = document.getElementById("chat-toggle");
    const chatBox = document.getElementById("chat-box");
    const sendBtn = document.getElementById("send-btn");
    const chatInput = document.getElementById("chat-input");
    const messagesDiv = document.getElementById("messages");
    const quickReplies = document.getElementById("quick-replies");
    const clearBtn = document.querySelector("#chat-header .clear-chat");

    // Toggle chat
    toggleBtn.addEventListener("click", ()=>{
        chatBox.style.display = "flex";
        toggleBtn.style.display="none";
        document.body.classList.add("chat-open");
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    });
    document.querySelector("#chat-header .close-chat").addEventListener("click", ()=>{
        chatBox.style.display="none";
        toggleBtn.style.display="flex";
        document.body.classList.remove("chat-open");
    });

    // Append message
    function appendMessage(sender, text){
        const msg = document.createElement("div");
        msg.classList.add("message", sender==="You" ? "user" : "bot");

        const icon = sender === "You" ? USER_ICON : BOT_ICON;
        const timeStr = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });

        const textHtml = `<span class="text">${icon ? icon + ' ' : ''}${escapeHtml(text)}</span><span class="time">${timeStr}</span>`;
        msg.innerHTML = textHtml;
        messagesDiv.appendChild(msg);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
        if(sender !== "You" && ENABLE_SOUND) try { sound.play(); } catch(e){}
    }

    function escapeHtml(s){
        return String(s).replace(/[&<>"']/g, function(m){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m];});
    }

    function saveMessages(){ localStorage.setItem('chatMessages', messagesDiv.innerHTML); }
    function loadMessages(){
        const prev = localStorage.getItem('chatMessages');
        if(prev) messagesDiv.innerHTML = prev;
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }
    loadMessages();

    async function sendMessage(){
        const message = chatInput.value.trim();
        if(!message) return;
        appendMessage("You", message);
        chatInput.value="";
        saveMessages();

        const typing = document.createElement("div");
        typing.classList.add("message","bot","typing");
        typing.innerHTML = '<span class="text"><span class="dots"><span></span><span></span><span></span></span></span>';
        messagesDiv.appendChild(typing);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;

        try {
            const res = await fetch(API_URL,{
                method:"POST",
                headers:{"Content-Type":"application/json"},
                body:JSON.stringify({message})
            });
            const data = await res.json();
            typing.remove();
            appendMessage("Bot", data.reply);
            saveMessages();
        } catch(err){
            typing.remove();
            appendMessage("Bot","Connection error 😢");
            saveMessages();
        }
    }

    if(clearBtn){
        clearBtn.addEventListener("click", ()=>{
            if(confirm("<?php echo esc_js(__('Are you sure you want to clear the chat?', 'ai-assistant-chatbot-full-control')); ?>")){
                messagesDiv.innerHTML = '';
                saveMessages();
            }
        });
    }

    document.querySelectorAll(".quick-btn").forEach(btn=>{
        btn.addEventListener("click", ()=>{
            chatInput.value = btn.innerText;
            sendMessage();
        });
    });

    chatInput.addEventListener("keypress", e=>{
        if(e.key==="Enter") sendMessage();
    });
    sendBtn.addEventListener("click", sendMessage);
})();
</script>
