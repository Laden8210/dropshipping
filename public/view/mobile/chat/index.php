<?php
// Mobile Chat Interface for Support Tickets
// Provides a chat-like interface for mobile users
?>

<div class="chat-container">
    <!-- Chat Header -->
    <div class="chat-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0" id="ticketSubject">Support Chat</h5>
                <small class="text-muted" id="onlineIndicator" style="display: none;">
                    <i class="fas fa-circle text-success"></i> Support agent online
                </small>
            </div>
            <div>
                <span class="badge" id="ticketStatus">Open</span>
            </div>
        </div>
    </div>

    <!-- Chat Messages -->
    <div class="chat-messages" id="chatContainer">
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading conversation...</p>
        </div>
    </div>

    <!-- Chat Input -->
    <div class="chat-input">
        <div class="input-group">
            <textarea 
                class="form-control" 
                id="messageInput" 
                rows="2" 
                placeholder="Type your message..."
                style="resize: none; border-radius: 20px 0 0 20px;"
            ></textarea>
            <button 
                class="btn btn-primary" 
                id="sendButton"
                style="border-radius: 0 20px 20px 0;"
            >
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<!-- Hidden inputs for chat manager -->
<input type="hidden" id="ticketId" value="<?php echo $_GET['ticket_id'] ?? ''; ?>">
<input type="hidden" id="userId" value="<?php echo $_SESSION['auth']['user_id'] ?? ''; ?>">
<input type="hidden" id="userRole" value="<?php echo $_SESSION['auth']['role'] ?? ''; ?>">

<script src="assets/js/realtime-chat.js"></script>

<script>
// Mobile-specific chat functionality
document.addEventListener('DOMContentLoaded', function() {
    const ticketId = document.getElementById('ticketId').value;
    
    if (ticketId) {
        loadTicketInfo(ticketId);
    }
    
    // Auto-resize textarea
    const messageInput = document.getElementById('messageInput');
    if (messageInput) {
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        });
    }
});

function loadTicketInfo(ticketId) {
    new GetRequest({
        getUrl: 'api/support/get-messages.php',
        params: { ticket_id: ticketId },
        showLoading: false,
        showSuccess: false,
        callback: (err, data) => {
            if (err) {
                console.error('Error loading ticket info:', err);
                document.getElementById('chatContainer').innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p>Failed to load conversation</p>
                    </div>
                `;
            } else {
                updateTicketHeader(data.ticket);
                displayInitialMessages(data.messages);
            }
        }
    }).send();
}

function updateTicketHeader(ticket) {
    document.getElementById('ticketSubject').textContent = ticket.subject;
    
    const statusBadge = document.getElementById('ticketStatus');
    statusBadge.textContent = ticket.status.replace('_', ' ').toUpperCase();
    statusBadge.className = 'badge ' + getStatusClass(ticket.status);
}

function displayInitialMessages(messages) {
    const chatContainer = document.getElementById('chatContainer');
    
    if (!messages || messages.length === 0) {
        chatContainer.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                <p class="text-muted">No messages yet. Start the conversation!</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    messages.forEach(message => {
        html += createMessageHTML(message);
    });
    
    chatContainer.innerHTML = html;
    
    // Scroll to bottom
    chatContainer.scrollTop = chatContainer.scrollHeight;
}

function createMessageHTML(message) {
    const isOwnMessage = message.sender_id === document.getElementById('userId').value;
    const isSystemMessage = message.sender_type === 'system';
    
    if (isSystemMessage) {
        return `
            <div class="message mb-3 text-center">
                <div class="alert alert-info d-inline-block">
                    <small><i class="fas fa-info-circle"></i> ${message.message}</small>
                </div>
            </div>
        `;
    }
    
    const messageClass = isOwnMessage ? 'text-end' : 'text-start';
    const bubbleClass = isOwnMessage ? 'bg-primary text-white' : 'bg-light';
    
    return `
        <div class="message mb-3 ${messageClass}">
            <div class="d-inline-block p-3 rounded ${bubbleClass}" style="max-width: 80%;">
                <div class="message-content">${formatMessage(message.message)}</div>
                <small class="text-muted">
                    ${formatTime(message.created_at)}
                </small>
            </div>
        </div>
    `;
}

function formatMessage(message) {
    // Convert URLs to clickable links
    const urlRegex = /(https?:\/\/[^\s]+)/g;
    return message.replace(urlRegex, '<a href="$1" target="_blank" class="text-decoration-none">$1</a>');
}

function formatTime(timestamp) {
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

function getStatusClass(status) {
    switch (status) {
        case 'open': return 'bg-warning';
        case 'in_progress': return 'bg-info';
        case 'resolved': return 'bg-success';
        case 'closed': return 'bg-secondary';
        default: return 'bg-light';
    }
}
</script>

<style>
.chat-container {
    height: 100vh;
    display: flex;
    flex-direction: column;
    background-color: #f8f9fa;
}

.chat-header {
    background: white;
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
    background-color: #f8f9fa;
}

.chat-input {
    background: white;
    padding: 1rem;
    border-top: 1px solid #dee2e6;
    box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
}

.message {
    margin-bottom: 1rem;
}

.message-content {
    word-wrap: break-word;
    line-height: 1.4;
}

.input-group textarea {
    border-right: none;
}

.input-group button {
    border-left: none;
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .chat-container {
        height: 100vh;
        height: -webkit-fill-available;
    }
    
    .chat-header {
        padding: 0.75rem;
    }
    
    .chat-messages {
        padding: 0.75rem;
    }
    
    .chat-input {
        padding: 0.75rem;
    }
    
    .message .d-inline-block {
        max-width: 85% !important;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .chat-container {
        background-color: #1a1a1a;
    }
    
    .chat-header,
    .chat-input {
        background: #2d2d2d;
        border-color: #404040;
    }
    
    .chat-messages {
        background-color: #1a1a1a;
    }
    
    .bg-light {
        background-color: #404040 !important;
        color: white !important;
    }
}
</style>
