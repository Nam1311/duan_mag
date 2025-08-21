<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Tư Vấn Thời Trang</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    {{--
    <link rel="stylesheet" href="{{asset('')}}css/chat.css"> --}}
    <link rel="stylesheet" href="/css/chat.css">



</head>

<body>
    <div class="chat-container">
        <div class="chat-header">
            <div class="ai-info">
                <h3>M A G Bot <span class="ai-badge">AI</span></h3>
                <p>Trợ lý thời trang thông minh</p>
            </div>

            <div class="close-chat" style="cursor: pointer">X</div>
        </div>

        <div class="chat-messages" id="chat-messages">
            <div class="message received ai-response">
                Xin chào! Tôi là CSKH MAG - trợ lý ảo thời trang. Tôi có thể giúp gì cho bạn hôm nay? 😊
                <div class="suggestions">
                    <div class="suggestion-chip">Tìm váy dự tiệc</div>
                    <div class="suggestion-chip">Áo khoác nam mới</div>
                    <div class="suggestion-chip">Tư vấn kích cỡ</div>
                    <div class="suggestion-chip">Phối đồ với quần jeans</div>
                </div>
            </div>
        </div>
        <div class="chat-input">
            <input type="text" id="message-input" placeholder="Nhập câu hỏi của bạn...">
            <button id="send-btn">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>

        <div id="suggestion-list" class="suggestion-list">
            <!-- JS sẽ inject HTML gợi ý tại đây -->
        </div>
    </div>


    {{-- <div id="dynamic-suggestions"></div> --}}


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chatMessages = document.getElementById('chat-messages');
            const messageInput = document.getElementById('message-input');
            const sendBtn = document.getElementById('send-btn');
            const suggestionChips = document.querySelectorAll('.suggestion-chip');
            const actionButtons = document.querySelectorAll('.action-btn');
            const dynamicSuggestions = document.getElementById('dynamic-suggestions');

            // Load chat history from localStorage
            loadChatHistory();

            function scrollToBottom() {
                // Multiple methods to ensure scrolling works
                chatMessages.scrollTop = chatMessages.scrollHeight;

                // Fallback method
                setTimeout(() => {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }, 50);

                // Force scroll with smooth behavior
                setTimeout(() => {
                    chatMessages.scrollTo({
                        top: chatMessages.scrollHeight,
                        behavior: 'smooth'
                    });
                }, 100);
            }

            function saveChatHistory() {
                const messages = Array.from(chatMessages.children).map(element => {
                    if (element.classList.contains('message')) {
                        return {
                            content: element.innerHTML,
                            isReceived: element.classList.contains('received'),
                            timestamp: Date.now()
                        };
                    } else if (element.classList.contains('suggestion-section')) {
                        return {
                            content: element.innerHTML,
                            type: 'suggestion-section',
                            timestamp: Date.now()
                        };
                    }
                    return null;
                }).filter(item => item !== null);

                localStorage.setItem('chatHistory', JSON.stringify(messages));
            }

            function loadChatHistory() {
                try {
                    const savedHistory = localStorage.getItem('chatHistory');
                    if (savedHistory) {
                        const messages = JSON.parse(savedHistory);

                        // Clear current messages except welcome message
                        const welcomeMessage = chatMessages.querySelector('.message.received.ai-response');
                        chatMessages.innerHTML = '';
                        if (welcomeMessage) {
                            chatMessages.appendChild(welcomeMessage);
                        }

                        // Restore messages
                        messages.forEach(msg => {
                            const messageDiv = document.createElement('div');
                            if (msg.type === 'suggestion-section') {
                                messageDiv.classList.add('suggestion-section');
                            } else {
                                messageDiv.classList.add('message');
                                messageDiv.classList.add(msg.isReceived ? 'received' : 'sent');
                                if (msg.isReceived) {
                                    messageDiv.classList.add('ai-response');
                                }
                            }
                            messageDiv.innerHTML = msg.content;
                            chatMessages.appendChild(messageDiv);
                        });

                        // Auto scroll to bottom after loading history
                        setTimeout(() => {
                            scrollToBottom();
                        }, 100);
                    }
                } catch (error) {
                    console.error('Error loading chat history:', error);
                }
            }

            function addMessage(message, isReceived) {
                const messageDiv = document.createElement('div');
                messageDiv.classList.add('message');
                messageDiv.classList.add(isReceived ? 'received' : 'sent');
                if (isReceived) {
                    messageDiv.classList.add('ai-response');
                }
                if (isReceived) {
                    if (message.trim().startsWith('<')) {
                        messageDiv.innerHTML = message;
                    } else {
                        messageDiv.innerHTML = marked.parse(message);
                    }
                } else {
                    messageDiv.textContent = message;
                }
                chatMessages.appendChild(messageDiv);
                scrollToBottom();

                // Save chat history after adding message
                saveChatHistory();
            }

            async function sendMessage() {
                const message = messageInput.value.trim();
                if (message) {
                    addMessage(message, false);
                    messageInput.value = '';

                    const typingIndicator = document.createElement('div');
                    typingIndicator.classList.add('typing-indicator');
                    typingIndicator.innerHTML = `
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    `;
                    chatMessages.appendChild(typingIndicator);
                    scrollToBottom();

                    try {
                        const response = await fetch('http://127.0.0.1:8000/api/chatbox', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `my_instructions=${encodeURIComponent(message)}`
                        });

                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }

                        const data = await response.json();
                        if (data.status === 'success') {
                            chatMessages.removeChild(typingIndicator);

                            addMessage(data.message, true);

                            if (data.suggestions && data.suggestions.trim()) {
                                const block = document.createElement('div');
                                block.classList.add('suggestion-section');
                                block.innerHTML = data.suggestions;
                                chatMessages.appendChild(block);

                                // Auto scroll after adding suggestions
                                setTimeout(() => {
                                    scrollToBottom();
                                }, 100);

                                // Save chat history after adding suggestions
                                saveChatHistory();
                            }
                            updateDynamicSuggestions(data.suggestions);
                        } else {
                            throw new Error(data.detail || 'Có lỗi xảy ra');
                        }
                    } catch (error) {
                        chatMessages.removeChild(typingIndicator);
                        addMessage(`Xin lỗi, tôi gặp sự cố khi trả lời: ${error.message}`, true);
                    }
                }
            }

            function updateDynamicSuggestions(suggestions) {
                if (suggestions && suggestions.trim() !== "") {
                    dynamicSuggestions.innerHTML = suggestions;
                    dynamicSuggestions.style.display = 'block';
                } else {
                    dynamicSuggestions.innerHTML = '';
                    dynamicSuggestions.style.display = 'none';
                }

            }

            sendBtn.addEventListener('click', sendMessage);

            messageInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });

            suggestionChips.forEach(chip => {
                chip.addEventListener('click', function () {
                    messageInput.value = this.textContent;
                    sendMessage();
                });
            });

            actionButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const text = this.textContent.trim();
                    messageInput.value = text;
                    sendMessage();
                });
            });

            // Clear chat history functionality
            const clearChatBtn = document.querySelector('.clear-chat');
            if (clearChatBtn) {
                clearChatBtn.addEventListener('click', function() {
                    if (confirm('Bạn có muốn xóa toàn bộ lịch sử chat không?')) {
                        localStorage.removeItem('chatHistory');

                        // Reset to welcome message only
                        chatMessages.innerHTML = `
                            <div class="message received ai-response">
                                Xin chào! Tôi là CSKH MAG - trợ lý ảo thời trang. Tôi có thể giúp gì cho bạn hôm nay? 😊
                                <div class="suggestions">
                                    <div class="suggestion-chip">Tìm váy dự tiệc</div>
                                    <div class="suggestion-chip">Áo khoác nam mới</div>
                                    <div class="suggestion-chip">Tư vấn kích cỡ</div>
                                    <div class="suggestion-chip">Phối đồ với quần jeans</div>
                                </div>
                            </div>
                        `;

                        // Re-bind suggestion chips
                        const newSuggestionChips = document.querySelectorAll('.suggestion-chip');
                        newSuggestionChips.forEach(chip => {
                            chip.addEventListener('click', function () {
                                messageInput.value = this.textContent;
                                sendMessage();
                            });
                        });

                        scrollToBottom();
                    }
                });
            }

            scrollToBottom();
        });
    </script>
</body>

</html>
