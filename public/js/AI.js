        document.addEventListener('DOMContentLoaded', function () {
            const boxAvt = document.getElementById('avatar'); // Sử dụng ID thay vì class
            const boxAi = document.getElementById('box-ai');

            if (boxAvt && boxAi) {
                boxAvt.addEventListener('click', function () {
                    boxAvt.style.display = 'none';
                    boxAi.style.display = 'block';
                    
                    // Remove notification effect when avatar is clicked
                    boxAvt.classList.remove('has-notification');
                    
                    // Auto scroll to bottom when opening chat
                    setTimeout(() => {
                        const chatMessages = document.getElementById('chat-messages');
                        if (chatMessages) {
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                        }
                    }, 100);
                });
            }

            // Nếu bạn có nút đóng box chat, thêm đoạn này:
            const closeBtn = document.querySelector('.close-chat');
            if (closeBtn) {
                closeBtn.addEventListener('click', function () {
                    boxAi.style.display = 'none';
                    if (boxAvt) {
                        boxAvt.style.display = 'flex';
                    }
                });
            }
            
            // Handle dynamic close buttons that might be created later
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('close-chat')) {
                    boxAi.style.display = 'none';
                    if (boxAvt) {
                        boxAvt.style.display = 'flex';
                    }
                }
            });
        });
   