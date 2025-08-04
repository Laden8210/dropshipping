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
    status ENUM('active', 'inactive', 'archived') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_store_profile_user FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE products (
    product_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id CHAR(14) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_sku VARCHAR(100) NOT NULL,
    product_category BIGINT NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive', 'archived') DEFAULT 'active',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_user FOREIGN KEY (user_id) REFERENCES users(user_id),
    CONSTRAINT fk_products_category FOREIGN KEY (product_category) REFERENCES product_categories(category_id)
);

CREATE TABLE imported_product (
    imported_product_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id CHAR(14) NOT NULL,
    product_id BIGINT NOT NULL,
    store_id BIGINT NOT NULL,
    profit_margin DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_imported_product_user FOREIGN KEY (user_id) REFERENCES users(user_id),
    CONSTRAINT fk_imported_product_product FOREIGN KEY (product_id) REFERENCES products(product_id),
    CONSTRAINT fk_imported_product_store FOREIGN KEY (store_id) REFERENCES store_profile(store_id)
);

CREATE TABLE product_categories (
    category_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(255) NOT NULL UNIQUE,
    user_id CHAR(14) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_product_categories_user FOREIGN KEY (user_id) REFERENCES users(user_id)
);




CREATE TABLE product_price_history (
    history_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'USD',
    change_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_product_price_history_product FOREIGN KEY (product_id) REFERENCES products(pid)
);

CREATE TABLE product_images (
    image_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT NOT NULL,
    image_url TEXT NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_product_images_product FOREIGN KEY (product_id) REFERENCES products(pid)
);



CREATE TABLE inventory (
    inventory_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_inventory_product FOREIGN KEY (product_id) REFERENCES products(product_id)
);

CREATE TABLE stock_movements (
    movement_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    movement_number VARCHAR(50) NOT NULL UNIQUE,
    product_id BIGINT NOT NULL,
    inventory_id BIGINT NOT NULL,
    quantity INT NOT NULL,
    movement_type ENUM('in', 'out') NOT NULL,
    reason VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_stock_movements_product FOREIGN KEY (product_id) REFERENCES products(product_id),
    CONSTRAINT fk_stock_movements_inventory FOREIGN KEY (inventory_id) REFERENCES inventory(inventory_id)
);

CREATE TABLE warehouse (
    warehouse_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id CHAR(14) NOT NULL,
    warehouse_name VARCHAR(255) NOT NULL,
    warehouse_address VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_warehouse_user FOREIGN KEY (user_id) REFERENCES users(user_id)
);




create table cart (
    cart_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id CHAR(14) NOT NULL,
    product_id BIGINT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    store_id BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_cart_user FOREIGN KEY (user_id) REFERENCES users(user_id),
    CONSTRAINT fk_cart_product FOREIGN KEY (product_id) REFERENCES imported_product(product_id),
    CONSTRAINT fk_cart_store FOREIGN KEY (store_id) REFERENCES store_profile(store_id)
);




CREATE TABLE orders (
    order_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id CHAR(14) NOT NULL,
    store_id BIGINT NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    shipping_fee DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    tax DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(10, 2) NOT NULL,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    tracking_number VARCHAR(100) DEFAULT NULL,
    shipping_address_id BIGINT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(user_id),
    CONSTRAINT fk_orders_shipping_address FOREIGN KEY (shipping_address_id) REFERENCES user_shipping_address(address_id),
    CONSTRAINT fk_orders_store FOREIGN KEY (store_id) REFERENCES store_profile(store_id)
);


create table order_payments (
    payment_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    account_number VARCHAR(100) DEFAULT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    transaction_id VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_order_payments_order FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

CREATE TABLE order_status_history (
    status_history_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT NOT NULL,
    status ENUM(
        'pending',
        'processing',
        'shipped',
        'delivered',
        'completed',
        'cancelled',
        'refunded',
        'failed'
    ) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_status_history_order FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

create table order_shipping_status (
    shipping_status_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    remarks TEXT DEFAULT NULL,
    tracking_number VARCHAR(100) DEFAULT NULL,
    current_location VARCHAR(255) DEFAULT NULL,
    latitude DECIMAL(10, 8) DEFAULT NULL,
    longitude DECIMAL(11, 8) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_shipping_status_order FOREIGN KEY (order_id) REFERENCES orders(order_id)
);


CREATE TABLE user_shipping_address (
    address_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id CHAR(14) NOT NULL,
    address_line VARCHAR(255) NOT NULL,
    region VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    brgy VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_shipping_address_user FOREIGN KEY (user_id) REFERENCES users(user_id)

);

CREATE TABLE order_items (
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