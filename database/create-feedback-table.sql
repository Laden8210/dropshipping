-- Create user_feedback table
CREATE TABLE IF NOT EXISTS user_feedback (
    feedback_id VARCHAR(20) PRIMARY KEY,
    user_id CHAR(14) NOT NULL,
    feedback_type ENUM('bug_report', 'feature_request', 'improvement', 'general', 'complaint', 'compliment') NOT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    contact_email VARCHAR(150),
    is_anonymous BOOLEAN DEFAULT FALSE,
    status ENUM('pending', 'in_progress', 'resolved', 'closed') DEFAULT 'pending',
    admin_response TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_feedback_user FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Create index for better performance
CREATE INDEX idx_feedback_user_id ON user_feedback(user_id);
CREATE INDEX idx_feedback_status ON user_feedback(status);
CREATE INDEX idx_feedback_created_at ON user_feedback(created_at);
