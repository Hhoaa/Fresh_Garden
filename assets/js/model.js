// Model cho chatbot (placeholder)
const messages = [];

function addMessage(message, isUser) {
    messages.push({ text: message, isUser });
}

function getMessages() {
    return messages;
}