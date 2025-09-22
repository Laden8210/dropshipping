-- Support Tickets and Chat System Database Update
-- This script creates tables for customer support ticketing and messaging

-- Drop existing tables if they exist (in case of updates)
DROP TABLE IF EXISTS support_messages;
DROP TABLE IF EXISTS support_tickets;

-- Create support_tickets table
CREATE TABLE support_tickets (
    ticket_id VARCHAR(20) PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL,
    order_id BIGINT NOT NULL,
    store_id BIGINT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
    category ENUM('order_issue', 'product_question', 'shipping', 'payment', 'technical', 'other') DEFAULT 'other',
    assigned_to VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    customer_satisfaction INT NULL CHECK (customer_satisfaction >= 1 AND customer_satisfaction <= 5),
    
    -- Foreign key constraints
    CONSTRAINT fk_ticket_order FOREIGN KEY (order_id) REFERENCES orders(order_id),
    CONSTRAINT fk_ticket_store FOREIGN KEY (store_id) REFERENCES store_profile(store_id)
);

-- Create support_messages table
CREATE TABLE support_messages (
    message_id VARCHAR(20) PRIMARY KEY,
    ticket_id VARCHAR(20) NOT NULL,
    sender_id VARCHAR(50) NOT NULL,
    sender_type ENUM('customer', 'agent', 'system') NOT NULL,
    message TEXT NOT NULL,
    message_type ENUM('text', 'image', 'file', 'system') DEFAULT 'text',
    attachment_url VARCHAR(500) NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign key constraints
    CONSTRAINT fk_message_ticket FOREIGN KEY (ticket_id) REFERENCES support_tickets(ticket_id) ON DELETE CASCADE
);

-- Create indexes for better performance
CREATE INDEX idx_tickets_user_id ON support_tickets(user_id);
CREATE INDEX idx_tickets_store_id ON support_tickets(store_id);
CREATE INDEX idx_tickets_status ON support_tickets(status);
CREATE INDEX idx_tickets_priority ON support_tickets(priority);
CREATE INDEX idx_tickets_created_at ON support_tickets(created_at);
CREATE INDEX idx_tickets_assigned_to ON support_tickets(assigned_to);

CREATE INDEX idx_messages_ticket_id ON support_messages(ticket_id);
CREATE INDEX idx_messages_sender_id ON support_messages(sender_id);
CREATE INDEX idx_messages_created_at ON support_messages(created_at);
CREATE INDEX idx_messages_is_read ON support_messages(is_read);