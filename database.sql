CREATE TABLE users (
    user_id CHAR(14) PRIMARY KEY, 
    role VARCHAR(50) NOT NULL DEFAULT 'user', 
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone_number VARCHAR(20),
    birth_date DATE,
    gender ENUM('male', 'female') DEFAULT 'male',
    avatar_url TEXT, 
    password VARCHAR(255), 
    is_google_auth BOOLEAN DEFAULT FALSE, 
    google_id VARCHAR(50) UNIQUE NULL,
    is_email_verified BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL 
);


CREATE TABLE store_profile (
    store_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id CHAR(14) NOT NULL,
    store_name VARCHAR(255) NOT NULL,
    store_description TEXT,
    store_logo_url TEXT,
    store_address VARCHAR(255),
    store_phone VARCHAR(20),
    store_email VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_store_profile_user FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE imported_product (
    product_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id CHAR(14) NOT NULL,
    pid VARCHAR(100) NOT NULL UNIQUE,
    product_name VARCHAR(255) NOT NULL,
    supplier_id VARCHAR(100) NOT NULL,
    product_sku VARCHAR(100) NOT NULL,
    category VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive', 'archived') DEFAULT 'active',
    store_id BIGINT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,


    CONSTRAINT fk_imported_product_user FOREIGN KEY (user_id) REFERENCES users(user_id),
    CONSTRAINT fk_imported_product_store FOREIGN KEY (store_id) REFERENCES store_profile(store_id)
);





create table cart (
    cart_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id CHAR(14) NOT NULL,
    product_id BIGINT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_cart_user FOREIGN KEY (user_id) REFERENCES users(user_id),
    CONSTRAINT fk_cart_product FOREIGN KEY (product_id) REFERENCES imported_product(product_id)
);


create table orders (
    order_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id CHAR(14) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    order_number VARCHAR(50) NOT NULL UNIQUE,
    shipping_zip VARCHAR(20) NOT NULL,
    shipping_country VARCHAR(100) NOT NULL,
    shipping_country_code VARCHAR(10) NOT NULL,
    shipping_province VARCHAR(100) NOT NULL,
    shipping_city VARCHAR(100) NOT NULL,
    shipping_county VARCHAR(100) DEFAULT NULL,
    shipping_phone VARCHAR(20) NOT NULL,
    shipping_customer_name VARCHAR(255) NOT NULL,
    shipping_address VARCHAR(255) NOT NULL,
    shipping_address2 VARCHAR(255) DEFAULT NULL,
    tax_id VARCHAR(50) DEFAULT NULL,
    remark TEXT DEFAULT NULL,
    email VARCHAR(150) DEFAULT NULL,
    consignee_id VARCHAR(50) DEFAULT NULL,
    pay_type VARCHAR(50) DEFAULT NULL,
    shop_amount DECIMAL(10, 2) DEFAULT NULL,
    logistic_name VARCHAR(100) DEFAULT NULL,
    from_country_code VARCHAR(10) NOT NULL,
    house_number VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    

    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(user_id)
);

create table order_items (
    order_item_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT NOT NULL,
    product_id BIGINT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(order_id),
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES imported_product(product_id)
);


create table order_feedback (
    feedback_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT NOT NULL,
    user_id CHAR(14) NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_order_feedback_order FOREIGN KEY (order_id) REFERENCES orders(order_id),
    CONSTRAINT fk_order_feedback_user FOREIGN KEY (user_id) REFERENCES users(user_id)
);

create table order_feedback_comments (
    comment_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    feedback_id BIGINT NOT NULL,
    user_id CHAR(14) NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_feedback_comments_feedback FOREIGN KEY (feedback_id) REFERENCES order_feedback(feedback_id),
    CONSTRAINT fk_order_feedback_comments_user FOREIGN KEY (user_id) REFERENCES users(user_id)
);

create table order_feedback_replies (
    reply_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    comment_id BIGINT NOT NULL,
    user_id CHAR(14) NOT NULL,
    reply_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_order_feedback_replies_comment FOREIGN KEY (comment_id) REFERENCES order_feedback_comments(comment_id),
    CONSTRAINT fk_order_feedback_replies_user FOREIGN KEY (user_id) REFERENCES users(user_id)
);

create table support_tickets (
    ticket_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id CHAR(14) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('open', 'closed', 'pending') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_support_tickets_user FOREIGN KEY (user_id) REFERENCES users(user_id)
);

create table support_ticket_chat_logs (
    chat_log_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    ticket_id BIGINT NOT NULL,
    user_id CHAR(14) NOT NULL,
    message TEXT NOT NULL,
    is_agent BOOLEAN DEFAULT FALSE,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_support_ticket_chat_logs_ticket FOREIGN KEY (ticket_id) REFERENCES support_tickets(ticket_id),
    CONSTRAINT fk_support_ticket_chat_logs_user FOREIGN KEY (user_id) REFERENCES users(user_id)
);