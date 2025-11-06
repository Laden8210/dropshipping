-- Create admin user for testing
-- Password: Pa$$w0rd! (hashed with password_hash)



INSERT INTO users (
    user_id, 
    role, 
    first_name, 
    last_name, 
    email, 
    password, 
    is_active, 
    is_email_verified,
    created_at, 
    updated_at
) VALUES (
    'ADMIN001', 
    'admin', 
    'System', 
    'Administrator', 
    'admin@dropshipping.com', 
    '$2y$10$5IgNU0JtW9STNWcZMVpWG.O3OuuVQB9s5.5gqdBE.MKjk7tQfzwTi', 
    1, 
    1,
    NOW(), 
    NOW()
) ON DUPLICATE KEY UPDATE 
    role = 'admin',
    is_active = 1,
    updated_at = NOW();
