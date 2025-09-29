<div class="main-container" id="main-container">
    <div class="header-section text-center">
        <h2 class="mb-3">Customer Support Dashboard</h2>
        <p class="lead">Manage customer support tickets and conversations</p>
    </div>

    <div class="main-content">
        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-4" id="supportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">
                    <i class="fas fa-tachometer-alt me-2"></i>Overview
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tickets-tab" data-bs-toggle="tab" data-bs-target="#tickets" type="button" role="tab" aria-controls="tickets" aria-selected="false">
                    <i class="fas fa-ticket-alt me-2"></i>Manage Tickets
                            </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics" type="button" role="tab" aria-controls="analytics" aria-selected="false">
                    <i class="fas fa-chart-line me-2"></i>Performance Metrics
                            </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="supportTabContent">
            <!-- Overview Tab -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                <!-- Support Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h4 class="text-primary" id="totalTickets">0</h4>
                                <p class="text-muted">Total Tickets</p>
                            </div>
                        </div>
                                </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h4 class="text-warning" id="openTickets">0</h4>
                                <p class="text-muted">Open Tickets</p>
                            </div>
                        </div>
                                </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h4 class="text-info" id="inProgressTickets">0</h4>
                                <p class="text-muted">In Progress</p>
                            </div>
                        </div>
                                </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h4 class="text-success" id="resolvedTickets">0</h4>
                                <p class="text-muted">Resolved</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-md-6">
                <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-ticket-alt"></i> Recent Tickets</h5>
                        </div>
                            <div class="card-body">
                                <div id="recentTickets">
                                    <div class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Loading recent tickets...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Quick Stats</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h3 class="text-primary" id="avgResponseTime">0h</h3>
                                        <p class="text-muted">Avg Response Time</p>
                                    </div>
                                    <div class="col-6">
                                        <h3 class="text-success" id="resolutionRate">0%</h3>
                                        <p class="text-muted">Resolution Rate</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tickets Management Tab -->
            <div class="tab-pane fade" id="tickets" role="tabpanel" aria-labelledby="tickets-tab">
                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Filter Tickets</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="statusFilter" class="form-label">Status</label>
                                <select class="form-select" id="statusFilter" onchange="filterTickets()">
                                    <option value="">All Status</option>
                                    <option value="open">Open</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="priorityFilter" class="form-label">Priority</label>
                                <select class="form-select" id="priorityFilter" onchange="filterTickets()">
                                    <option value="">All Priority</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="categoryFilter" class="form-label">Category</label>
                                <select class="form-select" id="categoryFilter" onchange="filterTickets()">
                                    <option value="">All Categories</option>
                                    <option value="order_issue">Order Issue</option>
                                    <option value="product_question">Product Question</option>
                                    <option value="shipping">Shipping</option>
                                    <option value="payment">Payment</option>
                                    <option value="technical">Technical</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="searchTickets" class="form-label">Search</label>
                                <input type="text" class="form-control" id="searchTickets" placeholder="Search tickets..." onkeyup="searchTickets()">
                            </div>
                        </div>
                        </div>
                        </div>

                <!-- Tickets Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">All Support Tickets</h5>
                        <button class="btn btn-sm btn-outline-primary" onclick="loadTickets()">
                            <i class="fas fa-refresh"></i> Refresh
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Ticket ID</th>
                                        <th>Subject</th>
                                        <th>Customer</th>
                                        <th>Order #</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="ticketsTableBody">
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                        </div>
                                            <p class="mt-2">Loading tickets...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Tab -->
            <div class="tab-pane fade" id="analytics" role="tabpanel" aria-labelledby="analytics-tab">
                <!-- Date Range Filter -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Select Date Range</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="analyticsDateRange" class="form-label">Date Range</label>
                                <select class="form-select" id="analyticsDateRange" onchange="loadAnalyticsData()">
                                    <option value="7days">Last 7 Days</option>
                                    <option value="30days" selected>Last 30 Days</option>
                                    <option value="90days">Last 90 Days</option>
                                    <option value="thismonth">This Month</option>
                                    <option value="lastmonth">Last Month</option>
                                    <option value="thisyear">This Year</option>
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button class="btn btn-primary w-100" onclick="loadAnalyticsData()">
                                    <i class="fas fa-sync-alt"></i> Refresh Data
                                </button>
                            </div>
                        </div>
                    </div>
                                </div>

                <!-- Analytics Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h4 class="text-primary" id="analyticsTotalTickets">0</h4>
                                <p class="text-muted">Total Tickets</p>
                            </div>
                        </div>
                                </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h4 class="text-success" id="analyticsResolvedTickets">0</h4>
                                <p class="text-muted">Resolved Tickets</p>
                            </div>
                        </div>
                                </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h4 class="text-info" id="analyticsAvgResponseTime">0h 0m</h4>
                                <p class="text-muted">Avg. Response Time</p>
                            </div>
                        </div>
                                </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h4 class="text-warning" id="analyticsSatisfaction">0%</h4>
                                <p class="text-muted">Satisfaction Rate</p>
                            </div>
                        </div>
                    </div>
                                </div>

                <!-- Charts Section -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">Ticket Status Distribution</div>
                            <div class="card-body">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                                </div>
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">Ticket Priority Distribution</div>
                            <div class="card-body">
                                <canvas id="priorityChart"></canvas>
                            </div>
                        </div>
                    </div>
                                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header">Tickets Created Over Time</div>
                            <div class="card-body">
                                <canvas id="ticketsTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                            </div>

<!-- Ticket Details Modal -->
<div class="modal fade" id="ticketDetailsModal" tabindex="-1" aria-labelledby="ticketDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ticketDetailsModalLabel">Ticket #<span id="modalTicketId"></span> - <span id="modalTicketSubject"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header">Ticket Info</div>
                            <div class="card-body">
                                <p><strong>Customer:</strong> <span id="modalCustomerName"></span></p>
                                <p><strong>Order ID:</strong> <span id="modalOrderId"></span></p>
                                <p><strong>Status:</strong> <span id="modalTicketStatus" class="badge"></span></p>
                                <p><strong>Priority:</strong> <span id="modalTicketPriority" class="badge"></span></p>
                                <p><strong>Category:</strong> <span id="modalTicketCategory"></span></p>
                                <p><strong>Created:</strong> <span id="modalCreatedAt"></span></p>
                                <p><strong>Last Update:</strong> <span id="modalUpdatedAt"></span></p>
                                <div class="mt-3">
                                    <label for="updateStatus" class="form-label">Update Status</label>
                                    <select class="form-select" id="updateStatus" onchange="updateTicketStatus()">
                                        <option value="open">Open</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="resolved">Resolved</option>
                                        <option value="closed">Closed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                                </div>
                    <div class="col-md-8">
                        <div class="card chat-card">
                            <div class="card-header chat-header">Conversation</div>
                            <div class="card-body chat-body" id="chatMessages">
                                <div class="text-center text-muted py-3">Loading messages...</div>
                            </div>
                            <div class="card-footer chat-footer">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="chatInput" placeholder="Type your message...">
                                    <button class="btn btn-primary" type="button" onclick="sendMessage()"><i class="fas fa-paper-plane"></i> Send</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let allTickets = [];
let currentTicketId = null;
let chatInterval = null;
let statusChartInstance, priorityChartInstance, ticketsTrendChartInstance;

document.addEventListener('DOMContentLoaded', function() {
    loadSupportData();
    
    // Initialize tabs
    const tabTriggerList = [].slice.call(document.querySelectorAll('#supportTabs button'));
    tabTriggerList.forEach(function (tabTriggerEl) {
        tabTriggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            const tabId = event.target.getAttribute('data-bs-target').substring(1);
            
            if (tabId === 'tickets') {
                loadTickets();
            } else if (tabId === 'analytics') {
                loadAnalyticsData();
            }
        });
    });
});

function loadSupportData() {
    new GetRequest({
        getUrl: 'controller/user/support/get-tickets.php',
        params: {},
        showLoading: false,
        showSuccess: false,
        callback: (err, data) => {
            if (err) {
                console.error('Error loading support data:', err);
                Swal.fire({
                    title: 'Warning',
                    text: 'Some support data could not be loaded.',
                    icon: 'warning',
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                updateSupportStats(data.summary);
                displayRecentTickets(data.tickets.slice(0, 5));
            }
        }
    }).send();
}

function updateSupportStats(summary) {
    document.getElementById('totalTickets').textContent = summary.total_tickets;
    document.getElementById('openTickets').textContent = summary.open_tickets;
    document.getElementById('inProgressTickets').textContent = summary.in_progress_tickets;
    document.getElementById('resolvedTickets').textContent = summary.resolved_tickets;
    document.getElementById('avgResponseTime').textContent = summary.avg_resolution_time + 'h';
    
    const total_resolved = summary.resolved_tickets + summary.closed_tickets;
    const resolution_rate = summary.total_tickets > 0 ? 
        Math.round((total_resolved / summary.total_tickets) * 100) : 0;
    document.getElementById('resolutionRate').textContent = resolution_rate + '%';
}

function displayRecentTickets(tickets) {
    const container = document.getElementById('recentTickets');
    
    if (!tickets || tickets.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted">
                <i class="fas fa-ticket-alt fa-3x mb-3"></i>
                <p>No recent tickets found</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    tickets.forEach(ticket => {
        const priorityClass = getPriorityClass(ticket.priority);
        const statusClass = getStatusClass(ticket.status);
        
        html += `
            <div class="d-flex align-items-center mb-3 p-2 border rounded">
                <div class="me-3">
                    <span class="badge ${priorityClass}">${ticket.priority.toUpperCase()}</span>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1">${ticket.subject}</h6>
                    <small class="text-muted">
                        ${ticket.customer_name} • ${new Date(ticket.created_at).toLocaleDateString()}
                    </small>
                </div>
                <div>
                    <span class="badge ${statusClass}">${ticket.status.replace('_', ' ').toUpperCase()}</span>
                        </div>
                    </div>
        `;
    });
    
    container.innerHTML = html;
}

function loadTickets() {
    const status = document.getElementById('statusFilter').value;
    const priority = document.getElementById('priorityFilter').value;
    const category = document.getElementById('categoryFilter').value;

    const params = new URLSearchParams();
    if (status) params.append('status', status);
    if (priority) params.append('priority', priority);
    if (category) params.append('category', category);

    new GetRequest({
        getUrl: `controller/user/support/get-tickets.php?${params.toString()}`,
        showLoading: true,
        showSuccess: false,
        callback: (err, data) => {
            if (err) {
                console.error('Error loading tickets:', err);
                Swal.fire('Error', 'Failed to load tickets.', 'error');
                document.getElementById('ticketsTableBody').innerHTML = `<tr><td colspan="8" class="text-center text-danger">Failed to load tickets.</td></tr>`;
            } else {
                allTickets = data.tickets;
                displayTicketsTable(allTickets);
            }
        }
    }).send();
}

function displayTicketsTable(tickets) {
    const tbody = document.getElementById('ticketsTableBody');
    tbody.innerHTML = '';

    if (tickets.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted">No tickets found.</td></tr>`;
        return;
    }

    tickets.forEach(ticket => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${ticket.ticket_id}</td>
            <td>${ticket.subject}</td>
            <td>${ticket.customer_name}</td>
            <td>${ticket.order_number || 'N/A'}</td>
            <td><span class="badge bg-${getPriorityClass(ticket.priority)}">${ticket.priority}</span></td>
            <td><span class="badge bg-${getStatusClass(ticket.status)}">${ticket.status.replace('_', ' ')}</span></td>
            <td>${new Date(ticket.created_at).toLocaleDateString()}</td>
            <td>
                <button class="btn btn-sm btn-info" onclick="viewTicketDetails('${ticket.ticket_id}')">
                    <i class="fas fa-eye"></i> View
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function filterTickets() {
    loadTickets();
}

function searchTickets() {
    const searchTerm = document.getElementById('searchTickets').value.toLowerCase();
    const filtered = allTickets.filter(ticket =>
        ticket.ticket_id.toLowerCase().includes(searchTerm) ||
        ticket.subject.toLowerCase().includes(searchTerm) ||
        ticket.customer_name.toLowerCase().includes(searchTerm)
    );
    displayTicketsTable(filtered);
}

function viewTicketDetails(ticketId) {
    currentTicketId = ticketId;
    const ticket = allTickets.find(t => t.ticket_id === ticketId);

    if (ticket) {
        document.getElementById('modalTicketId').textContent = ticket.ticket_id;
        document.getElementById('modalTicketSubject').textContent = ticket.subject;
        document.getElementById('modalCustomerName').textContent = ticket.customer_name;
        document.getElementById('modalOrderId').textContent = ticket.order_number || 'N/A';
        document.getElementById('modalTicketStatus').textContent = ticket.status.replace('_', ' ');
        document.getElementById('modalTicketStatus').className = `badge bg-${getStatusClass(ticket.status)}`;
        document.getElementById('modalTicketPriority').textContent = ticket.priority;
        document.getElementById('modalTicketPriority').className = `badge bg-${getPriorityClass(ticket.priority)}`;
        document.getElementById('modalTicketCategory').textContent = ticket.category || 'N/A';
        document.getElementById('modalCreatedAt').textContent = new Date(ticket.created_at).toLocaleString();
        document.getElementById('modalUpdatedAt').textContent = new Date(ticket.updated_at).toLocaleString();
        document.getElementById('updateStatus').value = ticket.status;

        loadMessages(ticketId);
        if (chatInterval) clearInterval(chatInterval);
        chatInterval = setInterval(() => loadMessages(ticketId, true), 3000);

        const ticketDetailsModal = new bootstrap.Modal(document.getElementById('ticketDetailsModal'));
        ticketDetailsModal.show();

        document.getElementById('ticketDetailsModal').addEventListener('hidden.bs.modal', function () {
            if (chatInterval) clearInterval(chatInterval);
            chatInterval = null;
            currentTicketId = null;
        }, { once: true });
    }
}

function loadMessages(ticketId, isPolling = false) {
    new GetRequest({
        getUrl: `controller/user/support/get-ticket-details.php?ticket_id=${ticketId}`,
        showLoading: !isPolling,
        showSuccess: false,
        callback: (err, data) => {
            displayMessages(data.messages, data.ticket.user_id);
            
        }
    }).send();
}

function displayMessages(messages, customerUserId) {
    const chatMessagesDiv = document.getElementById('chatMessages');
    const shouldScroll = chatMessagesDiv.scrollHeight - chatMessagesDiv.clientHeight <= chatMessagesDiv.scrollTop + 50;

    chatMessagesDiv.innerHTML = '';
    if (messages.length === 0) {
        chatMessagesDiv.innerHTML = `<div class="text-center text-muted py-3">No messages yet.</div>`;
        return;
    }

    messages.forEach(msg => {
        const isCustomer = msg.sender_id === customerUserId;
        // check if system message, client or system agent
        const messageClass = msg.sender_type === 'system' ? 'system-message' : isCustomer ? 'customer-message' : 'agent-message';
        const senderName = msg.sender_type === 'system' ? 'System' : msg.sender_type === 'customer' ? msg.sender_name : 'Support Agent';
        const alignmentClass = isCustomer ? 'justify-content-end' : 'justify-content-start';

        const messageHtml = `
            <div class="d-flex ${alignmentClass} mb-2">
                <div class="message-bubble ${messageClass}">
                    <div class="message-sender">${msg.sender_name}</div>
                    <div class="message-text">${msg.message}</div>
                    ${msg.attachment_url ? `<div class="message-attachment"><a href="${msg.attachment_url}" target="_blank"><i class="fas fa-paperclip"></i> Attachment</a></div>` : ''}
                    <div class="message-time">${new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
                            </div>
                        </div>
        `;
        chatMessagesDiv.innerHTML += messageHtml;
    });

    if (shouldScroll) {
        chatMessagesDiv.scrollTop = chatMessagesDiv.scrollHeight;
    }
}

function sendMessage() {
    const chatInput = document.getElementById('chatInput');
    const message = chatInput.value.trim();

    if (!message || !currentTicketId) {
        Swal.fire('Warning', 'Message cannot be empty.', 'warning');
        return;
    }

    new PostRequest({
        postUrl: 'controller/user/support/send-reply.php',
        params: {
            ticket_id: currentTicketId,
            message: message
        },
        showLoading: false,
        showSuccess: false,
        callback: (err, data) => {
            if (err) {
                console.error('Error sending message:', err);
                Swal.fire('Error', 'Failed to send message.', 'error');
            } else {
                chatInput.value = '';
                loadMessages(currentTicketId);
                loadTickets();
            }
        }
    }).send();
}

function updateTicketStatus() {
    const newStatus = document.getElementById('updateStatus').value;
    if (!currentTicketId || !newStatus) {
        Swal.fire('Error', 'Invalid ticket or status.', 'error');
        return;
    }

    Swal.fire({
        title: 'Update Ticket Status',
        text: `Are you sure you want to change the status to "${newStatus.replace('_', ' ')}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, update it!',
        cancelButtonText: 'No, cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            new PostRequest({
                postUrl: 'controller/user/support/update-ticket-status.php',
                params: {
                    ticket_id: currentTicketId,
                    status: newStatus
                },
                showLoading: true,
                showSuccess: true,
                callback: (err, data) => {
                    if (err) {
                        console.error('Error updating status:', err);
                        Swal.fire('Error', 'Failed to update ticket status.', 'error');
                    } else {
                        loadTickets();
                        document.getElementById('modalTicketStatus').textContent = newStatus.replace('_', ' ');
                        document.getElementById('modalTicketStatus').className = `badge bg-${getStatusClass(newStatus)}`;
                    }
                }
            }).send();
        }
    });
}

function loadAnalyticsData() {
    const dateRange = document.getElementById('analyticsDateRange').value;

    new GetRequest({
        getUrl: `controller/user/support/get-kpi-data.php?period=${dateRange}`,
        showLoading: true,
        showSuccess: false,
        callback: (err, data) => {
            
            const chartsData = data.charts;
            const summary = data.summary;
            updateCharts(chartsData);
            updateAnalyticsSummaryCards(summary);
        }
    }).send();
}

function updateAnalyticsSummaryCards(summary) {

    document.getElementById('analyticsTotalTickets').textContent = summary.total_tickets;

    document.getElementById('analyticsResolvedTickets').textContent = summary.resolved_tickets;

    document.getElementById('analyticsAvgResponseTime').textContent = formatDuration(summary.avg_response_time);

    document.getElementById('analyticsSatisfaction').textContent = `${summary.satisfaction_score}%`;

}



function updateCharts(chartsData) {
    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    if (statusChartInstance) statusChartInstance.destroy();
    statusChartInstance = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: chartsData.status.labels,
            datasets: [{
                data: chartsData.status.data,
                backgroundColor: ['#ffc107', '#17a2b8', '#28a745', '#6c757d'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' },
                title: { display: false }
            }
        }
    });

    // Priority Chart
    const priorityCtx = document.getElementById('priorityChart').getContext('2d');
    if (priorityChartInstance) priorityChartInstance.destroy();

    priorityChartInstance = new Chart(priorityCtx, {
        type: 'pie',
        data: {
            labels: chartsData.priority.labels,
            datasets: [{
                data: chartsData.priority.data,
                backgroundColor: chartsData.priority.colors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' },
                title: { display: false }
            }
        }
    });

    // Tickets Trend Chart
    const ticketsTrendCtx = document.getElementById('ticketsTrendChart').getContext('2d');
    if (ticketsTrendChartInstance) ticketsTrendChartInstance.destroy();
    ticketsTrendChartInstance = new Chart(ticketsTrendCtx, {
        type: 'line',
        data: {
            labels: chartsData.tickets_trend.labels,
            datasets: [{
                label: 'Tickets Created',
                data: chartsData.tickets_trend.data,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                title: { display: false }
            },
            scales: {
                y: { beginAtZero: true },
                x: { }
            }
        }
    });
}

function resetAnalyticsDisplay() {
    document.getElementById('analyticsTotalTickets').textContent = '0';
    document.getElementById('analyticsResolvedTickets').textContent = '0';
    document.getElementById('analyticsAvgResponseTime').textContent = '0h 0m';
    document.getElementById('analyticsSatisfaction').textContent = '0%';

    if (statusChartInstance) statusChartInstance.destroy();
    if (priorityChartInstance) priorityChartInstance.destroy();
    if (ticketsTrendChartInstance) ticketsTrendChartInstance.destroy();
}

function formatDuration(seconds) {
    if (seconds === null || isNaN(seconds)) return 'N/A';
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const remainingSeconds = Math.floor(seconds % 60);
    
    let result = [];
    if (hours > 0) result.push(`${hours}h`);
    if (minutes > 0) result.push(`${minutes}m`);
    if (remainingSeconds > 0 || result.length === 0) result.push(`${remainingSeconds}s`);
    
    return result.join(' ');
}

function getPriorityClass(priority) {
    switch (priority) {
        case 'urgent': return 'danger';
        case 'high': return 'warning';
        case 'medium': return 'info';
        case 'low': return 'success';
        default: return 'secondary';
    }
}

function getStatusClass(status) {
    switch (status) {
        case 'open': return 'warning';
        case 'in_progress': return 'info';
        case 'resolved': return 'success';
        case 'closed': return 'secondary';
        default: return 'light';
    }
}
</script>

<style>
/* Chat specific styles */
.chat-card {
    height: 600px;
    display: flex;
    flex-direction: column;
}

.chat-body {
    flex-grow: 1;
    overflow-y: auto;
    padding: 1rem;
    background-color: #e9ecef;
    border-bottom: 1px solid #dee2e6;
}

.message-bubble {
    max-width: 75%;
    padding: 0.75rem 1rem;
    border-radius: 1rem;
    margin-bottom: 0.5rem;
    position: relative;
    word-wrap: break-word;
}

.customer-message {
    background-color: #007bff;
    color: white;
    align-self: flex-end;
    border-bottom-right-radius: 0.25rem;
}

.agent-message {
    background-color: #f8f9fa;
    color: #212529;
    border: 1px solid #dee2e6;
    align-self: flex-start;
    border-bottom-left-radius: 0.25rem;
}

.system-message {
    background-color: #f8f9fa;
    color: #212529;
    border: 1px solid #dee2e6;
    align-self: center;
    border-radius: 0.25rem;
}


.message-sender {
    font-weight: bold;
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
    opacity: 0.8;
}

.message-time {
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.7);
    text-align: right;
    margin-top: 0.5rem;
}

.agent-message .message-time {
    color: rgba(33, 37, 41, 0.7);
}

.chat-footer {
    padding: 1rem;
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

/* Tab styles */
.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: 0.375rem;
    border-top-right-radius: 0.375rem;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
}

.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.nav-tabs .nav-link i {
    margin-right: 0.5rem;
}
</style>