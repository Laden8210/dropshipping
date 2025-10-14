-- Drop existing variation tables and create simplified ones
DROP TABLE IF EXISTS product_variation_images;
DROP TABLE IF EXISTS product_variation_attributes;
DROP TABLE IF EXISTS product_variations;

-- Create simplified product variations table
CREATE TABLE product_variations_simple (
    variation_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT NOT NULL,
    size VARCHAR(50) DEFAULT NULL COMMENT 'Product size (e.g., Small, Medium, Large)',
    color VARCHAR(50) DEFAULT NULL COMMENT 'Product color (e.g., Red, Blue, Black)',
    weight DECIMAL(10,2) DEFAULT NULL COMMENT 'Weight in grams for this variation',
    length DECIMAL(10,2) DEFAULT NULL COMMENT 'Length in cm for this variation',
    width DECIMAL(10,2) DEFAULT NULL COMMENT 'Width in cm for this variation',
    height DECIMAL(10,2) DEFAULT NULL COMMENT 'Height in cm for this variation',
    price DECIMAL(10,2) NOT NULL COMMENT 'Price for this specific variation',
    currency VARCHAR(10) DEFAULT 'USD',
    sku_suffix VARCHAR(20) DEFAULT NULL COMMENT 'Additional SKU identifier',
    stock_quantity INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_product_variations_simple_product FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    INDEX idx_product_variations_simple_product (product_id),
    INDEX idx_product_variations_simple_size (size),
    INDEX idx_product_variations_simple_color (color),
    UNIQUE KEY unique_product_variation_simple (product_id, size, color)
);


-- Update inventory table to work with simplified variations
ALTER TABLE inventory 
DROP FOREIGN KEY fk_inventory_variation,
DROP INDEX idx_inventory_variation,
ADD CONSTRAINT fk_inventory_variation_simple FOREIGN KEY (variation_id) REFERENCES product_variations_simple(variation_id) ON DELETE CASCADE,
ADD INDEX idx_inventory_variation_simple (variation_id);

-- Update stock movements to work with simplified variations
ALTER TABLE stock_movements 
DROP FOREIGN KEY fk_stock_movements_variation,
DROP INDEX idx_stock_movements_variation,
ADD CONSTRAINT fk_stock_movements_variation_simple FOREIGN KEY (variation_id) REFERENCES product_variations_simple(variation_id) ON DELETE CASCADE,
ADD INDEX idx_stock_movements_variation_simple (variation_id);

-- Update order items to work with simplified variations
ALTER TABLE order_items 
DROP FOREIGN KEY fk_order_items_variation,
DROP INDEX idx_order_items_variation,
ADD CONSTRAINT fk_order_items_variation_simple FOREIGN KEY (variation_id) REFERENCES product_variations_simple(variation_id) ON DELETE SET NULL,
ADD INDEX idx_order_items_variation_simple (variation_id);

-- Update cart to work with simplified variations
ALTER TABLE cart 
DROP FOREIGN KEY fk_cart_variation,
DROP INDEX idx_cart_variation,
ADD CONSTRAINT fk_cart_variation_simple FOREIGN KEY (variation_id) REFERENCES product_variations_simple(variation_id) ON DELETE CASCADE,
ADD INDEX idx_cart_variation_simple (variation_id);

-- Update imported_product to work with simplified variations
ALTER TABLE imported_product 
DROP FOREIGN KEY fk_imported_product_variation,
DROP INDEX idx_imported_product_variation,
ADD CONSTRAINT fk_imported_product_variation_simple FOREIGN KEY (variation_id) REFERENCES product_variations_simple(variation_id) ON DELETE CASCADE,
ADD INDEX idx_imported_product_variation_simple (variation_id);
