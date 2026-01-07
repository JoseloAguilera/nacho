-- ============================================
-- Base de Datos: Sistema Nacho
-- Sistema de Gestión Comercial
-- ============================================

CREATE DATABASE IF NOT EXISTS nacho CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nacho;

-- ============================================
-- Tabla: users
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'ventas') NOT NULL DEFAULT 'ventas',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB;

-- ============================================
-- Tabla: categories
-- ============================================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB;

-- ============================================
-- Tabla: products
-- ============================================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_code (code),
    INDEX idx_name (name),
    INDEX idx_category (category_id)
) ENGINE=InnoDB;

-- ============================================
-- Tabla: customers
-- ============================================
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    document VARCHAR(50),
    phone VARCHAR(50),
    email VARCHAR(100),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_document (document)
) ENGINE=InnoDB;

-- ============================================
-- Tabla: suppliers
-- ============================================
CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    document VARCHAR(50),
    phone VARCHAR(50),
    email VARCHAR(100),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_document (document)
) ENGINE=InnoDB;

-- ============================================
-- Tabla: sales
-- ============================================
CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    user_id INT NOT NULL,
    sale_number VARCHAR(50) NOT NULL UNIQUE,
    date DATE NOT NULL,
    payment_type ENUM('cash', 'credit') NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    tax DECIMAL(10, 2) NOT NULL DEFAULT 0,
    total DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'partial', 'paid', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_sale_number (sale_number),
    INDEX idx_date (date),
    INDEX idx_customer (customer_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- ============================================
-- Tabla: sale_details
-- ============================================
CREATE TABLE sale_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    INDEX idx_sale (sale_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB;

-- ============================================
-- Tabla: purchases
-- ============================================
CREATE TABLE purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NOT NULL,
    user_id INT NOT NULL,
    purchase_number VARCHAR(50) NOT NULL UNIQUE,
    date DATE NOT NULL,
    payment_type ENUM('cash', 'credit') NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    tax DECIMAL(10, 2) NOT NULL DEFAULT 0,
    total DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'partial', 'paid', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_purchase_number (purchase_number),
    INDEX idx_date (date),
    INDEX idx_supplier (supplier_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- ============================================
-- Tabla: purchase_details
-- ============================================
CREATE TABLE purchase_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    INDEX idx_purchase (purchase_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB;

-- ============================================
-- Tabla: customer_payments (Cobranzas)
-- ============================================
CREATE TABLE customer_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    customer_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_method ENUM('cash', 'transfer', 'check', 'card') NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE RESTRICT,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
    INDEX idx_sale (sale_id),
    INDEX idx_customer (customer_id),
    INDEX idx_date (payment_date)
) ENGINE=InnoDB;

-- ============================================
-- Tabla: supplier_payments (Pagos a Proveedores)
-- ============================================
CREATE TABLE supplier_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT NOT NULL,
    supplier_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_method ENUM('cash', 'transfer', 'check', 'card') NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE RESTRICT,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT,
    INDEX idx_purchase (purchase_id),
    INDEX idx_supplier (supplier_id),
    INDEX idx_date (payment_date)
) ENGINE=InnoDB;

-- ============================================
-- Datos de Prueba
-- ============================================

-- Usuario administrador (password: admin123)
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@nacho.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('vendedor1', 'ventas@nacho.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ventas');

-- Categorías de ejemplo
INSERT INTO categories (name, description) VALUES 
('Electrónica', 'Productos electrónicos y tecnología'),
('Alimentos', 'Productos alimenticios'),
('Bebidas', 'Bebidas en general'),
('Limpieza', 'Productos de limpieza');

-- Productos de ejemplo
INSERT INTO products (category_id, code, name, description, price, stock) VALUES 
(1, 'PROD001', 'Mouse Inalámbrico', 'Mouse inalámbrico USB', 15.99, 50),
(1, 'PROD002', 'Teclado USB', 'Teclado estándar USB', 25.50, 30),
(2, 'PROD003', 'Arroz 1kg', 'Arroz blanco premium', 2.50, 100),
(3, 'PROD004', 'Coca Cola 2L', 'Bebida gaseosa', 3.00, 80),
(4, 'PROD005', 'Detergente 500ml', 'Detergente líquido', 4.50, 60);

-- Clientes de ejemplo
INSERT INTO customers (name, document, phone, email, address) VALUES 
('Juan Pérez', '12345678', '555-0001', 'juan@email.com', 'Calle Principal 123'),
('María García', '87654321', '555-0002', 'maria@email.com', 'Avenida Central 456'),
('Carlos López', '11223344', '555-0003', 'carlos@email.com', 'Plaza Mayor 789');

-- Proveedores de ejemplo
INSERT INTO suppliers (name, document, phone, email, address) VALUES 
('Distribuidora Tech S.A.', '20123456789', '555-1001', 'ventas@tech.com', 'Zona Industrial 100'),
('Alimentos del Sur', '20987654321', '555-1002', 'info@alimentos.com', 'Mercado Central 200');
