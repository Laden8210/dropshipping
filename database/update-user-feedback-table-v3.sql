-- Simplified user_feedback table for mobile API
-- Only essential fields needed

-- Drop existing table if it exists
DROP TABLE IF EXISTS user_feedback;

-- Create simplified user_feedback table
CREATE TABLE user_feedback (
    feedback_id VARCHAR(20) PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL, 
    store_id BIGINT NOT NULL,
    order_id BIGINT NOT NULL,
    product_id BIGINT NOT NULL,
    rating INT DEFAULT NULL CHECK (rating >= 1 AND rating <= 5),
    review TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add foreign key constraints
ALTER TABLE user_feedback ADD CONSTRAINT fk_feedback_product FOREIGN KEY (product_id) REFERENCES products(product_id);
ALTER TABLE user_feedback ADD CONSTRAINT fk_feedback_store FOREIGN KEY (store_id) REFERENCES store_profile(store_id);
ALTER TABLE user_feedback ADD CONSTRAINT fk_feedback_order FOREIGN KEY (order_id) REFERENCES orders(order_id);

