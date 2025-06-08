<div class="main-container" id="main-container">
    <div class="header-section text-center">
        <p class="lead">Respond to customer inquiries and monitor chatbot performance</p>
    </div>

    <div class="">
        <!-- Support Stats -->
        <div class="support-stats">
            <div class="stat-card">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-content">
                    <h3>142</h3>
                    <p>Total Tickets</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>24</h3>
                    <p>Pending Tickets</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>98</h3>
                    <p>Resolved Tickets</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="stat-content">
                    <h3>76%</h3>
                    <p>Chatbot Resolution Rate</p>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="row">
            <!-- Ticket List -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>Customer Tickets</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-filter"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="ticket-item active">
                            <div class="ticket-priority priority-high"></div>
                            <div class="ticket-content">
                                <h6 class="mb-0">Order not delivered on time</h6>
                                <div class="ticket-meta">
                                    <span class="text-muted">John Smith</span>
                                    <span class="ticket-status status-open">Open</span>
                                </div>
                            </div>
                        </div>
                        <div class="ticket-item">
                            <div class="ticket-priority priority-medium"></div>
                            <div class="ticket-content">
                                <h6 class="mb-0">Product damaged during shipping</h6>
                                <div class="ticket-meta">
                                    <span class="text-muted">Emily Davis</span>
                                    <span class="ticket-status status-pending">Pending</span>
                                </div>
                            </div>
                        </div>
                        <div class="ticket-item">
                            <div class="ticket-priority priority-low"></div>
                            <div class="ticket-content">
                                <h6 class="mb-0">Question about return policy</h6>
                                <div class="ticket-meta">
                                    <span class="text-muted">Robert Johnson</span>
                                    <span class="ticket-status status-open">Open</span>
                                </div>
                            </div>
                        </div>
                        <div class="ticket-item">
                            <div class="ticket-priority priority-high"></div>
                            <div class="ticket-content">
                                <h6 class="mb-0">Payment failed but money deducted</h6>
                                <div class="ticket-meta">
                                    <span class="text-muted">Sarah Williams</span>
                                    <span class="ticket-status status-open">Open</span>
                                </div>
                            </div>
                        </div>
                        <div class="ticket-item">
                            <div class="ticket-priority priority-medium"></div>
                            <div class="ticket-content">
                                <h6 class="mb-0">Need assistance with product setup</h6>
                                <div class="ticket-meta">
                                    <span class="text-muted">Michael Brown</span>
                                    <span class="ticket-status status-resolved">Resolved</span>
                                </div>
                            </div>
                        </div>
                        <div class="ticket-item">
                            <div class="ticket-priority priority-low"></div>
                            <div class="ticket-content">
                                <h6 class="mb-0">Inquiry about product specifications</h6>
                                <div class="ticket-meta">
                                    <span class="text-muted">Jennifer Taylor</span>
                                    <span class="ticket-status status-resolved">Resolved</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <!-- Chat Interface -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Support Conversation</h5>
                        <div>
                            <span class="badge bg-primary">Ticket #TSK-142</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="chat-container" id="chatContainer">
                            <div class="d-flex flex-column">
                                <div class="message customer-message">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="customer-avatar me-2">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <strong>John Smith</strong>
                                    </div>
                                    <p>Hi, my order #ORD-2023-142 hasn't arrived yet. It was supposed to be delivered two days ago.</p>
                                    <div class="message-time">Today, 10:24 AM</div>
                                </div>

                                <div class="message bot-message">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="customer-avatar me-2" style="background-color: #e2e8f0; color: #4a5568;">
                                            <i class="fas fa-robot"></i>
                                        </div>
                                        <strong>Support Bot</strong>
                                    </div>
                                    <p>I'm sorry to hear that. Let me check the status of your order.</p>
                                    <div class="message-time">Today, 10:25 AM</div>
                                </div>

                                <div class="message agent-message">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="customer-avatar me-2" style="background-color: var(--primary-color);">
                                            <i class="fas fa-headset"></i>
                                        </div>
                                        <strong>You (Support Agent)</strong>
                                    </div>
                                    <p>Hello John, I've checked your order status. It appears there was a delay at our distribution center. Your order is now in transit and scheduled for delivery tomorrow.</p>
                                    <div class="message-time">Today, 10:30 AM</div>
                                </div>

                                <div class="message customer-message">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="customer-avatar me-2">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <strong>John Smith</strong>
                                    </div>
                                    <p>Thank you for the update. Can you confirm the delivery time?</p>
                                    <div class="message-time">Today, 10:32 AM</div>
                                </div>
                            </div>
                        </div>

                        <div class="p-3">
                            <div class="chat-input">
                                <textarea placeholder="Type your response here..."></textarea>
                                <button class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-paperclip"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-smile"></i>
                                    </button>
                                </div>
                                <button class="btn btn-sm btn-success">
                                    <i class="fas fa-check-circle me-1"></i>Resolve Ticket
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chatbot Logs -->
            <div class="col-lg-4">

                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Chatbot Performance</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                            <div class="text-center">
                                <h3 class="mb-0">76%</h3>
                                <p class="text-muted mb-0">Resolution Rate</p>
                            </div>
                            <div class="text-center">
                                <h3 class="mb-0">24%</h3>
                                <p class="text-muted mb-0">Escalation Rate</p>
                            </div>
                            <div class="text-center">
                                <h3 class="mb-0">4.2</h3>
                                <p class="text-muted mb-0">Avg. Rating</p>
                            </div>
                        </div>

                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 76%"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small>Successful Resolutions</small>
                            <small>76%</small>
                        </div>

                        <div class="progress mb-2 mt-3" style="height: 10px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 24%"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small>Escalated to Agent</small>
                            <small>24%</small>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-robot me-2"></i>Chatbot Performance Logs</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" href="#">Today</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">This Week</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">All Logs</a>
                            </li>
                        </ul>

                        <div class="p-3">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Search chatbot logs...">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <div style="max-height: 500px; overflow-y: auto;">
                            <div class="log-item log-customer">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="log-time">10:45 AM</span>
                                    <span><i class="fas fa-user me-2"></i><strong>Sarah Johnson</strong></span>
                                </div>
                                <p class="mb-0">How do I reset my password?</p>
                            </div>

                            <div class="log-item log-bot">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="log-time">10:45 AM</span>
                                    <span><i class="fas fa-robot me-2"></i><strong>Support Bot</strong></span>
                                    <span class="ms-2 log-success"><i class="fas fa-check-circle"></i> Resolved</span>
                                </div>
                                <p class="mb-0">You can reset your password by visiting our password reset page...</p>
                            </div>

                            <div class="log-item log-customer">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="log-time">10:30 AM</span>
                                    <span><i class="fas fa-user me-2"></i><strong>Mike Peterson</strong></span>
                                </div>
                                <p class="mb-0">I need to return a defective product</p>
                            </div>

                            <div class="log-item log-bot">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="log-time">10:31 AM</span>
                                    <span><i class="fas fa-robot me-2"></i><strong>Support Bot</strong></span>
                                    <span class="ms-2 log-success"><i class="fas fa-check-circle"></i> Resolved</span>
                                </div>
                                <p class="mb-0">Our return process is simple. Please visit the returns section...</p>
                            </div>

                            <div class="log-item log-customer">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="log-time">10:15 AM</span>
                                    <span><i class="fas fa-user me-2"></i><strong>Emily Chen</strong></span>
                                </div>
                                <p class="mb-0">I have a billing question about my last invoice</p>
                            </div>

                            <div class="log-item log-bot">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="log-time">10:16 AM</span>
                                    <span><i class="fas fa-robot me-2"></i><strong>Support Bot</strong></span>
                                    <span class="ms-2 log-warning"><i class="fas fa-exclamation-triangle"></i> Escalated</span>
                                </div>
                                <p class="mb-0">I've created a support ticket for you. Our billing team will contact you within 24 hours.</p>
                            </div>

                            <div class="log-item log-customer">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="log-time">09:58 AM</span>
                                    <span><i class="fas fa-user me-2"></i><strong>David Kim</strong></span>
                                </div>
                                <p class="mb-0">My order hasn't shipped yet</p>
                            </div>

                            <div class="log-item log-bot">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="log-time">09:59 AM</span>
                                    <span><i class="fas fa-robot me-2"></i><strong>Support Bot</strong></span>
                                    <span class="ms-2 log-success"><i class="fas fa-check-circle"></i> Resolved</span>
                                </div>
                                <p class="mb-0">I see your order is processing. Estimated shipping date is tomorrow...</p>
                            </div>

                            <div class="log-item log-customer">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="log-time">09:45 AM</span>
                                    <span><i class="fas fa-user me-2"></i><strong>Robert Taylor</strong></span>
                                </div>
                                <p class="mb-0">Do you offer international shipping?</p>
                            </div>

                            <div class="log-item log-bot">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="log-time">09:46 AM</span>
                                    <span><i class="fas fa-robot me-2"></i><strong>Support Bot</strong></span>
                                    <span class="ms-2 log-success"><i class="fas fa-check-circle"></i> Resolved</span>
                                </div>
                                <p class="mb-0">Yes, we ship to over 50 countries worldwide. Shipping rates vary by destination...</p>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Simulated ticket selection
    document.querySelectorAll('.ticket-item').forEach(item => {
        item.addEventListener('click', function() {
            document.querySelectorAll('.ticket-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');

            // Update chat header
            const ticketTitle = this.querySelector('h6').textContent;
            document.querySelector('.card-header h5').innerHTML = `<i class="fas fa-comments me-2"></i>Support Conversation - ${ticketTitle}`;
        });
    });

    // Auto-scroll chat to bottom
    const chatContainer = document.getElementById('chatContainer');
    chatContainer.scrollTop = chatContainer.scrollHeight;

    // Handle message sending
    document.querySelector('.fa-paper-plane').closest('button').addEventListener('click', function() {
        const textarea = document.querySelector('textarea');
        const message = textarea.value.trim();

        if (message) {
            const chatContainer = document.querySelector('.d-flex.flex-column');

            // Create new message
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message agent-message';
            messageDiv.innerHTML = `
                    <div class="d-flex align-items-center mb-2">
                        <div class="customer-avatar me-2" style="background-color: var(--primary-color);">
                            <i class="fas fa-headset"></i>
                        </div>
                        <strong>You (Support Agent)</strong>
                    </div>
                    <p>${message}</p>
                    <div class="message-time">Just now</div>
                `;

            chatContainer.appendChild(messageDiv);
            textarea.value = '';

            // Auto-scroll to bottom
            chatContainer.parentElement.scrollTop = chatContainer.parentElement.scrollHeight;

            // Simulate customer reply after a delay
            setTimeout(() => {
                const replyDiv = document.createElement('div');
                replyDiv.className = 'message customer-message';
                replyDiv.innerHTML = `
                        <div class="d-flex align-items-center mb-2">
                            <div class="customer-avatar me-2">
                                <i class="fas fa-user"></i>
                            </div>
                            <strong>John Smith</strong>
                        </div>
                        <p>Thank you for your help! That answers my question.</p>
                        <div class="message-time">Just now</div>
                    `;

                chatContainer.appendChild(replyDiv);

                // Auto-scroll to bottom
                chatContainer.parentElement.scrollTop = chatContainer.parentElement.scrollHeight;
            }, 2000);
        }
    });
</script>