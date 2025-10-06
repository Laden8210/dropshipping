-- Create admin user for testing
-- Password: admin123 (hashed with password_hash)

INSERT INTO users (
    user_id, 
    role, 
    first_name, 
    last_name, 
    email, 
    password, 
    is_active, 
    created_at, 
    updated_at
) VALUES (
    'ADMIN001', 
    'admin', 
    'System', 
    'Administrator', 
    'admin@dropshipping.com', 
    'admin123', 
    1, 
    NOW(), 
    NOW()
) ON DUPLICATE KEY UPDATE 
    role = 'admin',
    is_active = 1,
    updated_at = NOW();
