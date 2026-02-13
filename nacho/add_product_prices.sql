-- Add new price columns to products table
ALTER TABLE products
ADD COLUMN cost_price DECIMAL(10, 2) NOT NULL DEFAULT 0.00 AFTER description,
ADD COLUMN min_sale_price DECIMAL(10, 2) NOT NULL DEFAULT 0.00 AFTER price;

-- Update existing records to have some default values if needed (optional, defaults handle it)
