$(document).ready(function() {
    const $chatContainer = $('.chat-container');
    const $chatBody = $('.chat-body');
    const $chatInput = $('#chat-input');
    const $chatIcon = $('.chat-icon');
    const $closeChat = $('.close-chat');

    // Hiển thị/ẩn chatbot
    $chatIcon.on('click', function() {
        $chatContainer.toggle('fast'); // Sử dụng toggle với hiệu ứng
    });

    $closeChat.on('click', function() {
        $chatContainer.hide('fast'); // Ẩn với hiệu ứng
    });

    // Gửi tin nhắn
    $('.chat-footer button').on('click', function() {
        const message = $chatInput.val().trim();
        if (message) {
            addMessage(message, true);
            $chatBody.append(`<div style="text-align: right; margin: 10px 0;"><span style="background: #2ecc71; color: #fff; padding: 8px 15px; border-radius: 15px;">${message}</span></div>`);
            $chatInput.val('');

            // Phản hồi giả từ AI
            setTimeout(() => {
                addMessage("Xin chào! Tôi là trợ lý AI của Fresh Garden. Bạn cần giúp gì?", false);
                $chatBody.append(`<div style="text-align: left; margin: 10px 0;"><span style="background: #f1f1f1; color: #333; padding: 8px 15px; border-radius: 15px;">Xin chào! Tôi là trợ lý AI của Fresh Garden. Bạn cần giúp gì?</span></div>`);
                $chatBody.scrollTop($chatBody[0].scrollHeight); // Cuộn xuống cuối
            }, 1000);
        }
    });

    $chatInput.on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            $('.chat-footer button').click();
        }
    });

    // Cuộn lên đầu trang
    $('.scroll-top-button').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: 0 }, 500);
    });
});

// Model cho chatbot (placeholder)
const messages = [];

function addMessage(message, isUser) {
    messages.push({ text: message, isUser });
}

function getMessages() {
    return messages;
}