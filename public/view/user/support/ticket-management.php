<?php
// Admin Support Dashboard - Ticket Management
// Allows admins to view, manage, and respond to support tickets
?>

<div class="main-container">
    <div class="header-section text-center">
        <h2 class="mb-3">Support Ticket Management</h2>
        <p class="lead">Manage customer support tickets and conversations</p>
    </div>

    <div class="main-content">
        <!-- Summary Cards -->
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
                        <label for="storeFilter" class="form-label">Store</label>
                        <select class="form-select" id="storeFilter" onchange="filterTickets()">
                            <option value="">All Stores</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tickets Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Support Tickets</h5>
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
                                <th>Customer</th>
                                <th>Order</th>
                                <th>Subject</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Category</th>
                                <th>Store</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="ticketsTableBody">
                            <tr>
                                <td colspan="10" class="text-center">
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
</div>

<!-- Ticket Details Modal -->
<div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ticketModalLabel">Ticket Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="ticketDetails">
                    <!-- Ticket details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateTicketStatus()">Update Status</button>
            </div>
        </div>
    </div>
</div>

<script>
let allTickets = [];
let filteredTickets = [];
let currentTicket = null;

document.addEventListener('DOMContentLoaded', function() {
    loadTickets();
});

function loadTickets() {
        new GetRequest({
            getUrl: 'controller/user/support/get-tickets.php',
        params: {},
        showLoading: false,
        showSuccess: false,
        callback: (err, data) => {
            if (err) {
                console.error('Error loading tickets:', err);
                document.getElementById('ticketsTableBody').innerHTML = `
                    <tr>
                        <td colspan="10" class="text-center text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <p>Failed to load tickets</p>
                        </td>
                    </tr>
                `;
            } else {
                allTickets = data.tickets;
                filteredTickets = data.tickets;
                updateSummaryCards(data.summary);
                populateStoreFilter(data.tickets);
                displayTicketsTable(data.tickets);
            }
        }
    }).send();
}

function updateSummaryCards(summary) {
    document.getElementById('totalTickets').textContent = summary.total_tickets;
    document.getElementById('openTickets').textContent = summary.open_tickets;
    document.getElementById('inProgressTickets').textContent = summary.in_progress_tickets;
    document.getElementById('resolvedTickets').textContent = summary.resolved_tickets;
}

function populateStoreFilter(tickets) {
    const storeFilter = document.getElementById('storeFilter');
    const uniqueStores = [...new Set(tickets.map(ticket => ticket.store_name))];
    
    storeFilter.innerHTML = '<option value="">All Stores</option>';
    uniqueStores.forEach(storeName => {
        const option = document.createElement('option');
        option.value = storeName;
        option.textContent = storeName;
        storeFilter.appendChild(option);
    });
}

function filterTickets() {
    const statusFilter = document.getElementById('statusFilter').value;
    const priorityFilter = document.getElementById('priorityFilter').value;
    const categoryFilter = document.getElementById('categoryFilter').value;
    const storeFilter = document.getElementById('storeFilter').value;
    
    filteredTickets = allTickets.filter(ticket => {
        const matchesStatus = !statusFilter || ticket.status === statusFilter;
        const matchesPriority = !priorityFilter || ticket.priority === priorityFilter;
        const matchesCategory = !categoryFilter || ticket.category === categoryFilter;
        const matchesStore = !storeFilter || ticket.store_name === storeFilter;
        
        return matchesStatus && matchesPriority && matchesCategory && matchesStore;
    });
    
    displayTicketsTable(filteredTickets);
}

function displayTicketsTable(tickets) {
    const tbody = document.getElementById('ticketsTableBody');
    
    if (!tickets || tickets.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center text-muted">
                    <i class="fas fa-ticket-alt fa-3x mb-3"></i>
                    <p>No tickets found matching your criteria</p>
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    tickets.forEach(ticket => {
        const priorityClass = getPriorityClass(ticket.priority);
        const statusClass = getStatusClass(ticket.status);
        
        html += `
            <tr>
                <td><strong>${ticket.ticket_id}</strong></td>
                <td>${ticket.customer_name}</td>
                <td>
                    <strong>${ticket.order_number}</strong>
                    <br>
                    <small class="text-muted">₱${parseFloat(ticket.order_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</small>
                </td>
                <td>
                    <div class="ticket-subject">
                        <strong>${ticket.subject}</strong>
                        <br>
                        <small class="text-muted">${ticket.description.substring(0, 100)}${ticket.description.length > 100 ? '...' : ''}</small>
                    </div>
                </td>
                <td><span class="badge ${priorityClass}">${ticket.priority.toUpperCase()}</span></td>
                <td><span class="badge ${statusClass}">${ticket.status.replace('_', ' ').toUpperCase()}</span></td>
                <td><span class="badge bg-secondary">${ticket.category.replace('_', ' ').toUpperCase()}</span></td>
                <td>${ticket.store_name}</td>
                <td>
                    <small>${new Date(ticket.created_at).toLocaleDateString()}</small>
                    <br>
                    <small class="text-muted">${new Date(ticket.created_at).toLocaleTimeString()}</small>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="viewTicket('${ticket.ticket_id}')">
                        <i class="fas fa-eye"></i> View
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function viewTicket(ticketId) {
    currentTicket = ticketId;
    const modal = new bootstrap.Modal(document.getElementById('ticketModal'));
    modal.show();
    
    document.getElementById('ticketDetails').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading ticket details...</p>
        </div>
    `;
    
        new GetRequest({
            getUrl: 'controller/user/support/get-ticket-details.php',
        params: { ticket_id: ticketId },
        showLoading: false,
        showSuccess: false,
        callback: (err, data) => {
            if (err) {
                document.getElementById('ticketDetails').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        Failed to load ticket details.
                    </div>
                `;
            } else {
                displayTicketDetails(data);
            }
        }
    }).send();
}

function displayTicketDetails(data) {
    const container = document.getElementById('ticketDetails');
    
    let html = `
        <div class="row mb-4">
            <div class="col-md-8">
                <h4>${data.ticket.subject}</h4>
                <p class="text-muted">${data.ticket.description}</p>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6>Ticket Information</h6>
                        <p><strong>ID:</strong> ${data.ticket.ticket_id}</p>
                        <p><strong>Status:</strong> <span class="badge ${getStatusClass(data.ticket.status)}">${data.ticket.status.replace('_', ' ').toUpperCase()}</span></p>
                        <p><strong>Priority:</strong> <span class="badge ${getPriorityClass(data.ticket.priority)}">${data.ticket.priority.toUpperCase()}</span></p>
                        <p><strong>Category:</strong> ${data.ticket.category.replace('_', ' ').toUpperCase()}</p>
                        <p><strong>Created:</strong> ${new Date(data.ticket.created_at).toLocaleString()}</p>
                        <p><strong>Order:</strong> ${data.ticket.order_number}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <h5>Conversation</h5>
                <div class="chat-container" style="height: 400px; overflow-y: auto; border: 1px solid #dee2e6; padding: 15px; border-radius: 8px;">
    `;
    
    if (data.messages && data.messages.length > 0) {
        data.messages.forEach(message => {
            const messageClass = message.sender_type === 'customer' ? 'text-end' : 'text-start';
            const bubbleClass = message.sender_type === 'customer' ? 'bg-primary text-white' : 'bg-light';
            
            html += `
                <div class="message mb-3 ${messageClass}">
                    <div class="d-inline-block p-3 rounded ${bubbleClass}" style="max-width: 70%;">
                        <div class="message-content">${message.message}</div>
                        <small class="text-muted">${new Date(message.created_at).toLocaleString()}</small>
                    </div>
                </div>
            `;
        });
    } else {
        html += '<p class="text-muted text-center">No messages yet</p>';
    }
    
    html += `
                </div>
                
                <div class="mt-3">
                    <div class="input-group">
                        <textarea class="form-control" id="replyMessage" rows="3" placeholder="Type your reply..."></textarea>
                        <button class="btn btn-primary" onclick="sendReply()">
                            <i class="fas fa-paper-plane"></i> Send
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
}

function sendReply() {
    const message = document.getElementById('replyMessage').value.trim();
    
    if (!message) {
        Swal.fire({
            title: 'Error',
            text: 'Please enter a message',
            icon: 'error'
        });
        return;
    }
    
        new CreateRequest({
            postUrl: 'controller/user/support/send-reply.php',
        params: {
            ticket_id: currentTicket,
            message: message
        },
        showLoading: true,
        showSuccess: true,
        callback: (err, data) => {
            if (err) {
                console.error('Error sending reply:', err);
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to send reply',
                    icon: 'error'
                });
            } else {
                document.getElementById('replyMessage').value = '';
                viewTicket(currentTicket); // Refresh ticket details
            }
        }
    }).send();
}

function updateTicketStatus() {
    // Implementation for updating ticket status
    Swal.fire({
        title: 'Update Ticket Status',
        html: `
            <div class="mb-3">
                <label class="form-label">New Status</label>
                <select id="newStatus" class="form-select">
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Update',
        preConfirm: () => {
            const newStatus = document.getElementById('newStatus').value;
            if (!newStatus) {
                Swal.showValidationMessage('Please select a status');
                return false;
            }
            return { status: newStatus };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            new UpdateRequest({
                putUrl: 'controller/user/support/update-ticket-status.php',
                params: {
                    ticket_id: currentTicket,
                    status: result.value.status
                },
                showLoading: true,
                showSuccess: true,
                callback: (err, data) => {
                    if (err) {
                        console.error('Error updating status:', err);
                        Swal.fire({
                            title: 'Error',
                            text: 'Failed to update ticket status',
                            icon: 'error'
                        });
                    } else {
                        loadTickets(); // Refresh tickets list
                        viewTicket(currentTicket); // Refresh ticket details
                    }
                }
            }).send();
        }
    });
}

function getPriorityClass(priority) {
    switch (priority) {
        case 'urgent': return 'bg-danger';
        case 'high': return 'bg-warning';
        case 'medium': return 'bg-info';
        case 'low': return 'bg-success';
        default: return 'bg-secondary';
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
.card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.table th {
    background-color: #343a40;
    color: white;
    border: none;
    font-weight: 600;
}

.table td {
    vertical-align: middle;
}

.btn {
    border-radius: 6px;
}

.form-control, .form-select {
    border-radius: 6px;
}

.badge {
    font-size: 0.75em;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

.ticket-subject {
    max-width: 200px;
}

.chat-container {
    background-color: #f8f9fa;
}

.message-content {
    word-wrap: break-word;
}
</style>
