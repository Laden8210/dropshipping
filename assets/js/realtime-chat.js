// Real-time Chat Manager for Support Tickets
// Handles WebSocket-like functionality using polling

class RealtimeChatManager {
    constructor(ticketId, userId, userRole) {
        this.ticketId = ticketId;
        this.userId = userId;
        this.userRole = userRole;
        this.lastMessageId = '';
        this.pollingInterval = null;
        this.isPolling = false;
        this.chatContainer = null;
        this.messageInput = null;
        this.onlineStatusInterval = null;
        
        this.init();
    }
    
    init() {
        this.chatContainer = document.getElementById('chatContainer');
        this.messageInput = document.getElementById('messageInput');
        
        if (!this.chatContainer) {
            console.error('Chat container not found');
            return;
        }
        
        this.startPolling();
        this.updateUserStatus();
        this.setupEventListeners();
    }
    
    startPolling() {
        if (this.isPolling) return;
        
        this.isPolling = true;
        this.pollingInterval = setInterval(() => {
            this.checkForNewMessages();
        }, 2000); // Check every 2 seconds
        
        // Also check immediately
        this.checkForNewMessages();
    }
    
    stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
        this.isPolling = false;
    }
    
    async checkForNewMessages() {
        try {
            const params = new URLSearchParams({
                action: 'check_messages',
                ticket_id: this.ticketId,
                last_message_id: this.lastMessageId
            });
            
            const response = await fetch(`api/support/realtime-chat.php?${params}`);
            const data = await response.json();
            
            if (data.status === 'success' && data.data.messages.length > 0) {
                this.displayNewMessages(data.data.messages);
                this.lastMessageId = data.data.messages[data.data.messages.length - 1].message_id;
                this.scrollToBottom();
            }
        } catch (error) {
            console.error('Error checking for new messages:', error);
        }
    }
    
    displayNewMessages(messages) {
        messages.forEach(message => {
            this.addMessageToChat(message);
        });
    }
    
    addMessageToChat(message) {
        const messageElement = this.createMessageElement(message);
        this.chatContainer.appendChild(messageElement);
    }
    
    createMessageElement(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message mb-3';
        
        const isOwnMessage = message.sender_id === this.userId;
        const isSystemMessage = message.sender_type === 'system';
        
        if (isSystemMessage) {
            messageDiv.className += ' text-center';
            messageDiv.innerHTML = `
                <div class="alert alert-info d-inline-block">
                    <small><i class="fas fa-info-circle"></i> ${message.message}</small>
                </div>
            `;
        } else {
            messageDiv.className += isOwnMessage ? ' text-end' : ' text-start';
            
            const bubbleClass = isOwnMessage ? 'bg-primary text-white' : 'bg-light';
            
            messageDiv.innerHTML = `
                <div class="d-inline-block p-3 rounded ${bubbleClass}" style="max-width: 70%;">
                    <div class="message-content">${this.formatMessage(message.message)}</div>
                    <small class="text-muted">
                        ${message.sender_name} • ${this.formatTime(message.created_at)}
                    </small>
                </div>
            `;
        }
        
        return messageDiv;
    }
    
    formatMessage(message) {
        // Convert URLs to clickable links
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        return message.replace(urlRegex, '<a href="$1" target="_blank" class="text-decoration-none">$1</a>');
    }
    
    formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = now - date;
        
        if (diff < 60000) { // Less than 1 minute
            return 'Just now';
        } else if (diff < 3600000) { // Less than 1 hour
            return Math.floor(diff / 60000) + 'm ago';
        } else if (diff < 86400000) { // Less than 1 day
            return Math.floor(diff / 3600000) + 'h ago';
        } else {
            return date.toLocaleDateString();
        }
    }
    
    async sendMessage(messageText) {
        if (!messageText.trim()) return;
        
        try {
            const formData = new FormData();
            formData.append('ticket_id', this.ticketId);
            formData.append('message', messageText);
            
            const response = await fetch('api/support/send-message.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.status === 'success') {
                // Add message to chat immediately for better UX
                const message = {
                    message_id: data.data.message_id,
                    sender_id: this.userId,
                    sender_type: this.userRole === 'admin' ? 'agent' : 'customer',
                    sender_name: this.userRole === 'admin' ? 'Support Agent' : 'You',
                    message: messageText,
                    message_type: 'text',
                    created_at: data.data.created_at
                };
                
                this.addMessageToChat(message);
                this.lastMessageId = message.message_id;
                this.scrollToBottom();
                
                // Clear input
                this.messageInput.value = '';
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Error sending message:', error);
            Swal.fire({
                title: 'Error',
                text: 'Failed to send message: ' + error.message,
                icon: 'error'
            });
        }
    }
    
    setupEventListeners() {
        if (this.messageInput) {
            this.messageInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage(this.messageInput.value);
                }
            });
        }
        
        // Send button
        const sendButton = document.getElementById('sendButton');
        if (sendButton) {
            sendButton.addEventListener('click', () => {
                this.sendMessage(this.messageInput.value);
            });
        }
        
        // Update user status periodically
        this.onlineStatusInterval = setInterval(() => {
            this.updateUserStatus();
        }, 30000); // Every 30 seconds
        
        // Check online status
        this.checkOnlineStatus();
    }
    
    async updateUserStatus() {
        try {
            const formData = new FormData();
            formData.append('status', 'online');
            
            await fetch('api/support/realtime-chat.php?action=update_status', {
                method: 'POST',
                body: formData
            });
        } catch (error) {
            console.error('Error updating user status:', error);
        }
    }
    
    async checkOnlineStatus() {
        try {
            const params = new URLSearchParams({
                action: 'get_online_status',
                ticket_id: this.ticketId
            });
            
            const response = await fetch(`api/support/realtime-chat.php?${params}`);
            const data = await response.json();
            
            if (data.status === 'success') {
                this.updateOnlineIndicators(data.data.participants);
            }
        } catch (error) {
            console.error('Error checking online status:', error);
        }
    }
    
    updateOnlineIndicators(participants) {
        const onlineIndicator = document.getElementById('onlineIndicator');
        if (onlineIndicator && participants.length > 0) {
            const onlineUsers = participants.filter(p => p.is_online);
            if (onlineUsers.length > 0) {
                onlineIndicator.innerHTML = `
                    <i class="fas fa-circle text-success"></i>
                    ${onlineUsers.map(u => u.name).join(', ')} online
                `;
                onlineIndicator.style.display = 'block';
            } else {
                onlineIndicator.style.display = 'none';
            }
        }
    }
    
    scrollToBottom() {
        if (this.chatContainer) {
            this.chatContainer.scrollTop = this.chatContainer.scrollHeight;
        }
    }
    
    destroy() {
        this.stopPolling();
        
        if (this.onlineStatusInterval) {
            clearInterval(this.onlineStatusInterval);
        }
    }
}

// Auto-initialize chat if elements exist
document.addEventListener('DOMContentLoaded', function() {
    const ticketId = document.getElementById('ticketId')?.value;
    const userId = document.getElementById('userId')?.value;
    const userRole = document.getElementById('userRole')?.value;
    
    if (ticketId && userId && userRole) {
        window.chatManager = new RealtimeChatManager(ticketId, userId, userRole);
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (window.chatManager) {
        window.chatManager.destroy();
    }
});
